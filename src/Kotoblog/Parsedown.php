<?php

namespace Kotoblog;

use Guzzle\Http\Client;

class Parsedown extends \Parsedown
{
    protected function completeCodeBlock($Block)
    {
        $text = $Block['element']['text']['text'];

        if (false !== strpos($text,'gist.github.com')) {
            $client = new Client();
            $request = $client->get(sprintf('%s.js', trim($text)));
            $response = $request->send();

//            $script = file_get_contents(sprintf('%s.js', $text));
            $Block['element']['name'] = 'script';
            unset($Block['element']['handler']);
            $Block['element']['text'] = $response->getBody()->__toString();

            return $Block;
        }

        $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');

        $Block['element']['text']['text'] = $text;

        return $Block;
    }
}
