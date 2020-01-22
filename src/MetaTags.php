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
     * @var bool
     */
    protected $minimize;

    /**
     * MetaTags constructor.
     * @param string|null $indentation
     * @param bool $minimize
     */
    public function __construct(string $indentation = null, bool $minimize = false)
    {
        $this->minimize = $minimize;
        $this->indentation = empty($indentation) ? '    ' : $indentation;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
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
     * @param string $key
     * @param string $value
     * @return $this
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
     * @param string $title
     * @return $this
     */
    public function title(string $title): self
    {
        $tag = sprintf('<title>%s</title>', $title);
        $this->addTag($tag, 'title');

        return $this;
    }

    /**
     * @param array $schema
     * @return $this
     */
    public function jsonLd(array $schema):  self
    {
        $json = json_encode($schema, JSON_UNESCAPED_SLASHES | ($this->minimize ? null : JSON_PRETTY_PRINT));
        $script = sprintf('<script type="application/ld+json">%s</script>', $json);

        $this->addTag($script, 'json-ld');
        return $this;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
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
     * @param string $charset
     * @return $this
     */
    public function charset(string $charset): self
    {
        $tag = $this->makeTag('meta', [
            'charset' => $charset,
        ]);

        $this->addTag($tag, 'meta');

        return $this;
    }

    /**
     * @param string $robot
     * @return $this
     */
    public function robots(string $robot): self
    {
        $this->meta('robots', $robot);
        return $this;
    }

    /**
     * @param string $href
     * @param string|null $type
     * @return $this
     */
    public function favicon(string $href, string $type = null): self
    {
        return $this->link('icon', $href, [
            'type' => $type,
        ]);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function description(string $value): self
    {
        return $this->meta('description', $value);
    }

    /**
     * @param string $rel
     * @param string $href
     * @param array $attributes
     * @return $this
     */
    public function link(string $rel, string $href, array $attributes = []): self
    {
        $attributes = array_merge([
            'rel' => $rel,
            'href' => $href,], $attributes);

        $tag = $this->makeTag('link', $attributes);
        $this->addTag($tag, 'link');

        return $this;
    }

    /**
     * @param string $href
     * @param null $media
     * @return $this
     */
    public function stylesheet(string $href, $media = null): self
    {
        return $this->link('stylesheet', $href, [
            'type' =>   'text/css',
            'media' =>  $media,
        ]);
    }

    /**
     * @param string $style
     * @return $this
     */
    public function style(string $style): self
    {
        $tag = sprintf('<style type="text/css">%s</style>', $style);
        $this->addTag($tag, 'style');

        return $this;
    }

    /**
     * @param string $script
     * @return $this
     */
    public function script(string $script): self
    {
        $tag = sprintf('<script>%s</script>', $script);
        $this->addTag($tag, 'script');

        return $this;
    }

    /**
     * @param string $tagName
     * @param $attributes
     * @return string|null
     */
    protected function makeTag(string $tagName, $attributes): ?string
    {
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
     * @param string $tag
     * @param string $group
     */
    protected function addTag(string $tag, string $group): void
    {
        if(!key_exists($group, $this->tags)) {
            $this->tags[$group] = new MetaTagsGroup();
        }

        $this->tags[$group]->addMeta($tag);
    }

    /**
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

        if($this->minimize){
            return implode('', $html);
        }

        $html = count($html) > 0 ? sprintf("%s%s\n", $this->indentation, implode("\n" . $this->indentation, $html)) : '';
        $html = preg_replace(sprintf('#^%s#', $this->indentation), '', $html, 1);
        return $html;
    }

}