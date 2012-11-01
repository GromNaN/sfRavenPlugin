<?php

class sfRavenPluginConfiguration extends sfPluginConfiguration
{
  const CONFIG_PATH = 'config/raven.yml';

  /**
   * Initializes the plugin.
   *
   * This method is called after the plugin's classes have been added to sfAutoload.
   */
  public function initialize()
  {
    if ($this->configuration instanceof sfApplicationConfiguration)
    {
      $configCache = $this->configuration->getConfigCache();
      $configCache->registerConfigHandler(self::CONFIG_PATH, 'sfDefineEnvironmentConfigHandler', array(
        'prefix' => 'raven_',
      ));

      require $configCache->checkConfig(self::CONFIG_PATH);

      if (!sfConfig::get('raven_dsn'))
      {
        return;
      }

      $client = new sfRavenClient(sfConfig::get('raven_dsn'));

      $errorHandler = new Raven_ErrorHandler($client);
      $errorHandler->registerExceptionHandler();
      $errorHandler->registerErrorHandler(true, E_ERROR | E_PARSE | E_NOTICE | E_STRICT);
      $errorHandler->registerShutdownFunction(500);
    }
  }
}
