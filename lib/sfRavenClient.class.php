<?php

class sfRavenClient extends Raven_Client
{
  protected function get_user_data()
  {
    if (!sfContext::hasInstance())
    {
      return parent::get_user_data();
    }

    $context = sfContext::getInstance();
    $user = $context->getUser();

    if (!$user || null === $user->getGuardUser())
    {
      return parent::get_user_data();
    }

    return array(
      'sentry.interfaces.User' => array(
        'is_authenticated' => $user->isAnonymous() ? false : true,
        'id'               => session_id(),
        'username'         => $user->getUserName(),
      )
    );
  }

  protected function get_extra_data()
  {
    if (!sfContext::hasInstance())
    {
      return array();
    }

    $context = sfContext::getInstance();

    $extra = array(
      'sf_module_name' => $context->getModuleName(),
      'sf_action_name' => $context->getActionName(),
    );

    if ($conf = $context->getConfiguration())
    {
      $extra['sf_environment'] = $conf->getEnvironment();
    }

    if (($user = $context->getUser()) && null !== $user->getGuardUser())
    {
      $credentials = '';
      if ($user->isAnonymous())
      {
      }
      elseif ($user->isSuperAdmin())
      {
        $credentials = 'Super admin';
      }
      elseif (method_exists($user, 'listCredentials'))
      {
        $credentials = implode(', ' , $user->listCredentials());
      }

      $extra['sf_user_credentials'] = $credentials;
      $extra['sf_user_attributes'] = $user->getAttributeHolder()->getAll();
    }

    return $extra;
  }
}