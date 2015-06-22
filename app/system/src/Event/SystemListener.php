<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Finder\Event\FileAccessEvent;

class SystemListener implements EventSubscriberInterface
{
    /**
     * Dispatches the 'site' or 'admin' event.
     */
    public function onRequest($event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        App::trigger(App::isAdmin() ? 'admin' : 'site', [App::request()]);
    }

    /**
     * Registers the media storage folder
     *
     * @param FileAccessEvent $event
     */
    public function onSystemFinder(FileAccessEvent $event)
    {
        if (App::user()->hasAccess('system: manage storage | system: manage storage read only')) {
            $event->path('#^'.strtr(App::get('path.storage'), '\\', '/').'($|\/.*)#', App::user()->hasAccess('system: manage storage') ? 'w' : 'r');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'request'   => 'onRequest',
            'system.finder' => 'onSystemFinder'
        ];
    }
}
