<?php

/**
 * DokuWiki Plugin external preview (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author Anton Caesar <caesaranton700@yahoo.de>
 */
class action_plugin_extprev extends \dokuwiki\Extension\ActionPlugin
{

    /** @inheritDoc */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'handleConfig');
    }

    /**
     * Event handler for DOKUWIKI_STARTED
     *
     * @see https://www.dokuwiki.org/devel:events:DOKUWIKI_STARTED
     * @param Doku_Event $event Event object
     * @param mixed $param optional parameter passed when event was registered
     * @return void
     */
    public function handleConfig(Doku_Event $event, $param) {
        global $JSINFO;
        $JSINFO['plugin']['extprev'] = [
            'selector' => $this->getConf('selector'),
        ];
    }

}

