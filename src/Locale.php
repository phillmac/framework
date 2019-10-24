<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

use UserFrosting\Support\Repository\Loader\YamlFileLoader;

/**
 * Locale Class.
 *
 * Act as a container for a Locale data loaded from filesystem data
 *
 * @author Louis Charette
 */
class Locale implements LocaleInterface
{
    /**
     * @var string The locale identifier, ie "en_US" or "fr_FR"
     */
    protected $identifier = '';

    /**
     * @var string The locale config file path
     */
    protected $configFile = '';

    /**
     * @var array Locale config data, loaded from the locale YAML file
     */
    protected $config;

    /**
     * Create locale class.
     *
     * @param string      $identifier The locale identifier (ie. "en_US")
     * @param string|null $configFile The path to the locale config file
     */
    public function __construct(string $identifier, ?string $configFile = null)
    {
        $this->identifier = $identifier;
        $this->configFile = (isset($configFile)) ? $configFile : "locale://$identifier/locale.yaml";

        // Load locale config
        $this->loadConfig();
    }

    /**
     * Loads the config into the class property.
     *
     * @throws \UserFrosting\Support\Exception\FileNotFoundException if config file not found
     */
    protected function loadConfig(): void
    {
        $loader = new YamlFileLoader($this->configFile);
        $this->config = $loader->load(false);
    }

    /**
     * Returns the list of authors of the locale.
     *
     * @return string[] The list of authors
     */
    public function getAuthors(): array
    {
        return $this->config['authors'];
    }

    /**
     * Returns defined configuration file.
     *
     * @return string
     */
    public function getConfigFile(): string
    {
        return $this->configFile;
    }

    /**
     * Returns the locale indentifier.
     *
     * @return string
     */
    public function getIndentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Return the raw configuration data.
     *
     * @return (array|string)[]
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Return an array of parent locales.
     *
     * @return Locale[]
     */
    public function getDependentLocales(): array
    {
        $parents = $this->getDependentLocalesIdentifier();

        // Transform locale identifier to locale instance
        $locales = array_map(function ($value) {
            return new self($value);
        }, $parents);

        return $locales;
    }

    /**
     * Return a list of parent locale identifier (eg. [fr_FR, en_US]).
     *
     * @return string[]
     */
    public function getDependentLocalesIdentifier(): array
    {
        return (is_array($this->config['parents'])) ? $this->config['parents'] : [];
    }

    /**
     * Return the name of the locale, in English form.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->config['name'];
    }

    /**
     * Return the number representing the plural rule to use for this locale.
     *
     * @return int
     */
    public function getPluralRule(): int
    {
        if (isset($this->config['options']['plural'])) {
            return $this->config['options']['plural'];
        } else {
            return 1;
        }
    }

    /**
     * Return the localized version of the locale name.
     *
     * @return string
     */
    public function getLocalizedName(): string
    {
        return $this->config['localized_name'];
    }
}
