<?php
/*
 * Babbar Dashboard API
 *
 * Licensed under the MIT license. See LICENSE file in the project root for details.
 *
 * @copyright Copyright (c) 2023 Babbar
 * @license   https://opensource.org/license/mit/ MIT License
 *
 */

namespace App\Repositories\Actions;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class AbstractAction implements ActionInterface, JsonSerializable
{
    /**
     * Show confirmation modal before fire action
     */
    protected bool $confirm = false;

    protected ?array $params = null;

    protected ?string $title = null;

    protected ?array $actionPayload;

    public function __construct(?array $actionPayload = null)
    {
        $this->actionPayload = $actionPayload;
    }

    abstract public function handle(Builder $builder): mixed;

    public function jsonSerialize(): array
    {
        $actionKey = $this->getActionKey();

        return [
            'handler' => $actionKey,
            'title' => $this->getTitle(),
            'confirm' => $this->confirm,
            'params' => $this->params,
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?? ___('actions.' . $this->getActionKey());
    }

    public function setTitle(string $title): ActionInterface
    {
        $this->title = $title;

        return $this;
    }

    public function setParams(array $params): ActionInterface
    {
        $this->params = $params;

        return $this;
    }

    public function getActionKey(): string
    {
        /** @var string $classname */
        $classname = preg_replace('/Action$/', '', class_basename($this));
        return Str::snake($classname);
    }

    protected function getSelectionsFromPayload(): ?array
    {
        if (empty($this->actionPayload)) {
            return [];
        }

        /** @var array $selections */
        $selections = Arr::get($this->actionPayload, 'selections', []);

        return $selections;
    }

    protected function getActionParamsFromPayload(): ?array
    {
        if (empty($this->actionPayload)) {
            return null;
        }

        /** @var ?array $params */
        $params = Arr::get($this->actionPayload, 'params');

        return $params;
    }
}
