<?php

namespace Database\Factories;

use App\Enums\SerpEnum;
use App\Enums\StatusEnum;
use App\Models\Benchmark;
use App\Models\Host;
use App\Models\Keyword;
use App\Models\Project;
use App\Models\Url;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected int $nbKeywordsMin = 20;
    protected int $nbKeywordsMax = 40;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url(),
            'serp' => $this->faker->randomElement(SerpEnum::cases()),
            'description' => $this->faker->randomElement([null, $this->faker->text()]),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Project $project) {
            //
        })->afterCreating(function (Project $project) {

            $lang = $project->serp->locale();
            $host = $project->host;

            // Update top position
            $host->update($this->makeKeywordDistribution());

            // Create common keywords
            $host->keywords()->attach($this->makeKeywords($project, $lang));

            // Create similar hosts
            $host->similars()->attach($this->makeSimilarHostsScore($host, $lang));

            // Create benchmark
            $this->makeBenchmark($project);
        });
    }

    protected function makeBenchmark(Project $project)
    {
        $benchmark = Benchmark::create([
            'type' => \App\Services\BenchmarkService\Benchmarks\RankingFactorBenchmark::class,
        ]);

        $benchmark->started_at = now();
        $benchmark->finished_at = now();
        $benchmark->status = StatusEnum::DONE;
        $benchmark->save();
    }

    protected function makeKeywords(Project $project, string $lang)
    {
        $sharedKeywords = Keyword::where('lang', $lang)
            ->inRandomOrder()
            ->limit(rand(0, $this->nbKeywordsMax))
            ->get();

        $newKeywords = Keyword::factory()
            ->count(rand($this->nbKeywordsMin, $this->nbKeywordsMax))
            ->state([
                'lang' => $lang,
            ])->create();

        return $newKeywords->merge($sharedKeywords)
            ->mapWithKeys(function ($keyword) use ($project) {

                $url = Url::firstOrCreate([
                    'url' => $this->makeProjectUrl($project, $keyword->keywords),
                    'host_id' => $project->host_id,
                ]);

                $rank = rand(1, 100);

                // Create fake Google Serp for this keywords
                $keyword->serp()->attach($this->makeKeywordSerpCompetitorUrls($keyword, $url, $rank));

                return [
                    $keyword->getKey() => [
                        'url_id' => $url->getKey(),
                        'rank' => $rank,
                    ]
                ];
            });
    }

    protected function makeKeywordSerpCompetitorUrls(Keyword $keyword, Url $currentUrl, int $currentRank): array
    {
        $competitors = [];

        for ($i = 1; $i <= 20; $i++) {
            if ($currentRank == $i) {
                $url = $currentUrl;
            } else {
                $url = Url::firstOrCreate([
                    'url' => $this->faker->url(),
                ]);

                $url->host()->update($this->makeKeywordDistribution());
            }

            $competitors[$url->getKey()] = [
                'rank' => $i,
                'host_id' => $url->host_id,
            ];
        }

        return $competitors;
    }

    protected function makeSimilarHostsScore(Host $host, string $lang): array
    {
        $repository = resolve(\App\Repositories\HostRepository::class);
        $hostsWithSameKeywordsQuery = $repository->getHostsWithSameKeywordsQuery($host)->get();

        return $hostsWithSameKeywordsQuery->mapWithKeys(function ($host) use ($lang) {
            $score = $this->faker->optional($weight = 0.9)->randomFloat(2, 0, 1);
            $hostId = $host->getKey();

            return [$hostId => [
                'score' => $score,
                'lang' => $lang,
            ]];
        })->filter(fn($h) => $h['score'])
            ->toArray();
    }

    protected function makeProjectUrl(Project $project, string $keyword): string
    {
        return implode('', [
            $this->faker->randomElement(['http://', 'https://']),
            $project->hostname,
            '/',
            Str::slug($keyword),
        ]);
    }

    protected function makeKeywordDistribution()
    {
        return [
            'nb_kw_pos_1_10' => $this->faker->optional($weight = 0.4)
                ->numberBetween(0, 100),

            'nb_kw_pos_11_20' => $this->faker->optional($weight = 0.8)
                ->numberBetween(0, 10000),

            'nb_kw_pos_21plus' => $this->faker->optional($weight = 0.9)
                ->numberBetween(0, 10000),
        ];
    }
}
