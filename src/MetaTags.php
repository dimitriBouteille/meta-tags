<?php

namespace Dbout\MetaTags;

/**
 * Class MetaTags
 *
 * @package Dbout\MetaTags
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2019 Dimitri BOUTEILLE
 */
class MetaTags
{

    /**
     * @var array
     */
    protected $order = ['title', 'meta', 'og', 'twitter', 'geo', 'link', 'style', 'script', 'json-ld'];

    /**
     * @var string
     */
    protected $indentation = '    ';

    /**
     * @var MetaTagsGroup[]
     */
    protected $tags = [];

    /**
     * Function og
     *
     * @param string $key
     * @param string $value
     * @return MetaTags
     */
    public function og(string $key, string $value): self
    {
        $tag = $this->makeTag('meta', [
            'property' => sprintf('og:%s', $key),
            'content' => $value,
        ]);

        $this->addTag($tag, 'og');

        return $this;
    }

    /**
     * Function twitter
     *
     * @param string $key
     * @param string $value
     * @return MetaTags
     */
    public function twitter(string $key, string $value): self
    {
        $tag = $this->makeTag('meta', [
            'name' => sprintf('twitter:%s', $key),
            'content' => $value,
        ]);

        $this->addTag($tag, 'twitter');

        return $this;
    }

    /**
     * Function title
     *
     * @param string $title
     * @return MetaTags
     */
    public function title(string $title): self
    {
        $tag = sprintf('<title>%s</title>', $title);
        $this->addTag($tag, 'title');

        return $this;
    }

    /**
     * Function jsonLd
     *
     * @param array $schema
     * @return MetaTags
     */
    public function jsonLd(array $schema):  self
    {
        $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $script = sprintf('<script type="application/ld+json">%s</script>', $json);

        $this->addTag($script, 'json-ld');
        return $this;
    }

    /**
     * Function meta
     *
     * @param string|array $key
     * @param string|array $value
     * @return MetaTags
     */
    public function meta($key, $value = null): self
    {
        $attributes = [];
        if(is_string($key)) {
            $attributes = ['name' => $key];
        } else if(is_array($key)) {
            $attributes = $key;
        }

        if(is_array($value)) {
            $attributes = array_merge($attributes, $value);
        } else if(!empty($value)) {
            $attributes['content'] = $value;
        }

        $tag = $this->makeTag('meta', $attributes);
        $this->addTag($tag, 'meta');

        return $this;
    }

    /**
     * Function charset
     *
     * @param string $charset
     * @return MetaTags
     */
    public function charset(string $charset): self {

        $tag = $this->makeTag('meta', [
            'charset' => $charset,
        ]);

        $this->addTag($tag, 'meta');

        return $this;
    }

    /**
     * Function robots
     *
     * @param string $robot
     * @return MetaTags
     */
    public function robots(string $robot): self {

        $this->meta('robots', $robot);
        return $this;
    }

    /**
     * Function favicon
     *
     * @param string $href
     * @param string|null $type
     * @return MetaTags
     */
    public function favicon(string $href, string $type = null): self {

        return $this->link('icon', [
            'href' => $href,
            'type' => $type,
        ]);
    }

    /**
     * Function description
     *
     * @param string $value
     * @return MetaTags
     */
    public function description(string $value): self {

        return $this->meta('description', $value);
    }

    /**
     * Function link
     *
     * @param string $rel
     * @param $value
     * @param array $attributes
     * @return $this
     */
    public function link(string $rel, $value, array $attributes = []): self
    {
        $attributes = ['rel' => $rel];

        if(is_array($value)) {
            $attributes = array_merge($attributes, $value);
        } else {
            $attributes['href'] = $value;
        }

        $tag = $this->makeTag('link', $attributes);
        $this->addTag($tag, 'link');

        return $this;
    }

    /**
     * Function stylesheet
     *
     * @param string $href
     * @param null $media
     * @return MetaTags
     */
    public function stylesheet(string $href, $media = null): self
    {
        return $this->link('stylesheet', [
            'type' =>   'text/css',
            'media' =>  $media,
            'href' =>   $href,
        ]);
    }

    /**
     * Function style
     *
     * @param string $style
     * @return MetaTags
     */
    public function style(string $style): self
    {
        $tag = sprintf('<style type="text/css">%s</style>', $style);
        $this->addTag($tag, 'style');

        return $this;
    }

    /**
     * Function script
     *
     * @param string $script
     * @return MetaTags
     */
    public function script(string $script): self
    {
        $tag = sprintf('<script>%s</script>', $script);
        $this->addTag($tag, 'script');

        return $this;
    }

    /**
     * Function makeTag
     *
     * @param string $tagName
     * @param $attributes
     * @return string|null
     */
    protected function makeTag(string $tagName, $attributes): ?string {

        if(!empty($tagName)) {

            if(is_array($attributes)) {
                $attributes = $this->makeStrAttributes($attributes);
            }

            if(!empty($attributes)) {
                return sprintf('<%s %s>', $tagName, $attributes);
            }
        }

        return null;
    }

    /**
     * Function makeStrAttributes
     * ie : attr="value" secondAttr="value"
     *
     * @param array $attributes
     * @return string
     */
    protected function makeStrAttributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {

            if(is_bool($value)) {
                if($value == true) {
                    $attr = $key;
                }
            } else {
                $attr = sprintf('%s="%s"', $key, $value);
            }

            if(!empty($attr) && !empty($value)) {
                $html[] = $attr;
            }
        }

        return implode(' ', $html);
    }

    /**
     * Function addTag
     *
     * @param string $tag
     * @param string $group
     * @return void
     */
    protected function addTag(string $tag, string $group): void
    {
        if(!key_exists($group, $this->tags)) {
            $this->tags[$group] = new MetaTagsGroup();
        }

        $this->tags[$group]->addMeta($tag);
    }

    /**
     * Function render
     *
     * @param array $groups
     * @return string|null
     */
    public function render(array $groups = []): ?string
    {
        $groups = count($groups) > 0 ? $groups : $this->order;
        $html = [];

        foreach ($groups as $group) {

            if(key_exists($group, $this->tags)) {
                $tags = $this->tags[$group];
                foreach ($tags->get() as $tag) {
                    $html[] = $tag;
                }
            }
        }

        $html = count($html) > 0 ? sprintf("%s%s\n", $this->indentation, implode("\n" . $this->indentation, $html)) : '';
        $html = preg_replace(sprintf('#^%s#', $this->indentation), '', $html, 1);

        return $html;
    }

}