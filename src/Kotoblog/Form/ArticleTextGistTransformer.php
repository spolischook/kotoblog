<?php

namespace Kotoblog\Form;

use Github\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ArticleTextGistTransformer implements DataTransformerInterface
{
    /** @var \Github\Client */
    protected $githubClient;

    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    /**
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        $transformer = $this;

        $result = preg_replace_callback('#<\s*?code\b[^>]*>(.*?)</code\b[^>]*>#s', function ($match) use ($transformer) {
            $doc = new \DOMDocument();
            $doc->loadHTML($match[0]);

            $gistAttributes = array();
            foreach ($doc->getElementsByTagName('code')->item(0)->attributes as $attr => $value) {
                $gistAttributes[$attr] = $value->value;
            }

            $gist = $transformer->getGist($match[1], $gistAttributes);

            $response = $transformer->githubClient->api('gist')->create($gist);

            $embed = sprintf('    %s', $response['html_url']);
            return $embed;

//            return $response['html_url'];
        }, $value);

        return $result;
    }

    public function getGist($code, array $attributes)
    {
        foreach ($attributes as $key => $value) {

        }
        $language = array_key_exists('language', $attributes) ? $attributes['language'] : 'PHP';
        $filename = array_key_exists('filename', $attributes) ? $attributes['filename'] : uniqid('kotoblog_') . '.' .strtolower($language);
        $description = array_key_exists('description', $attributes) ? $attributes['description'] : '';

        $gist = array(
            'language'    => $language,
            'filename'    => $filename,
            'public'      => array_key_exists('public', $attributes) ? $attributes['public'] : true,
            'description' => $description,
        );

        $files = array(
            $filename => array(
                'filename' => $filename,
                'language' => $language,
                'content'  => $code,
            )
        );

        $gist['files'] = $files;

        return $gist;
    }
}
