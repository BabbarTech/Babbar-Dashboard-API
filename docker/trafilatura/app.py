# coding: utf8
from flask import Flask, request, redirect, url_for, flash, jsonify
import json
import trafilatura
import requests
import urllib.parse
from trafilatura.settings import use_config

app = Flask(__name__)

newconfig = use_config()
newconfig.set("DEFAULT", "EXTRACTION_TIMEOUT", "0")

blacklist = {''}
@app.route('/analyze-on-page', methods=['POST'])

def main():
    content = request.get_json()
    url = content['url']
    try:

        parsed_url = urllib.parse.urlparse(content['url'])

        if parsed_url.netloc in blacklist:
            return jsonify({'error' : 'unauthorized'})

    except Exception as e:
        return jsonify({'error' : 'Not a valid url'})


    user_agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:107.0) Gecko/20100101 Firefox/107.0"

    headers = {
            'User-Agent': '{}'.format(user_agent),
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language': 'fr_FR;q=0.5',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
            'Pragma': 'no-cache',
            'Cache-Control': 'no-cache',
    }

    try:
        y = requests.get(url, headers=headers, timeout=5)
    except requests.exceptions.RequestException as e:
        return jsonify({'error' : 'out'})

    if y.status_code == 200:
        return jsonify({'url' : url, 'content' : trafilatura.extract(y.content, config=newconfig).replace('|', ' ')})
    else:
        return jsonify({'error' : y.status_code})

if __name__ == "__main__":
    app.run()