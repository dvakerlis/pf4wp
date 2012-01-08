<?php

/*
 * Copyright (c) 2011 Mike Green <myatus@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pf4wp\Help;

use Pf4wp\WordpressPlugin;

/**
 * Object class that provides resource-based help screens
 *
 * The resource is a simple INI-style file, containing the folowing
 * <code>
 * [id]
 * title = The help page title (optional)
 * content = The help page contents (HTML ok)
 * </code>
 *
 * The resource should at least contain an `id` with "overview" (case sensitive). For
 * WordPress 3.3+, a "side_tab" `id` is also supported, which ignores the title.
 *
 * @author Mike Green <myatus@gmail.com>
 * @package Pf4wp
 * @subpackage Help
 * @api
 */
class ContextHelp
{
    /** Back reference to owner object
     * @internal
     */
    protected $owner;
    
    /** Name of the help context
     * @internal
     */
    protected $name = '';
    
    /** Structure holding the various help tabs
     * @internal
     */
    protected $help_sections = false;
    
    /**
     * Construct
     *
     * @param WordpressPlugin $owner Owner object
     * @param string $name Name of the help context
     * @api
     */
    public function __construct(WordpressPlugin $owner, $name)
    {
        $this->owner = $owner;
        $this->name  = $name;
        
        $resource = $this->owner->getPluginDir() . \Pf4wp\WordpressPlugin::RESOURCES_DIR . 'help/' . $name . '.ini';
        
        if (@is_file($resource) && @is_readable($resource))
            $this->help_sections = parse_ini_file($resource, true);
    }
    
    /**
     * Returns the default content as a string, if read directly
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->help_sections && array_key_exists('overview', $this->help_sections) && array_key_exists('content', $this->help_sections['overview']))
            return $this->help_sections['overview']['content'];
            
        return '';
    }
    
    /**
     * Adds all help tabs (WP 3.3+)
     *
     * @param object $screen Screen to add help tabs to
     * @api
     */
    public function addTabs($screen)
    {
        if (!is_array($this->help_sections) || !is_object($screen) || !is_callable(array($screen, 'add_help_tab')))
            return;
            
        foreach ($this->help_sections as $help_section_key => $help_section) {
            $title   = (array_key_exists('title', $help_section)) ? $help_section['title'] : ucfirst($help_section_key);
            $content = (array_key_exists('content', $help_section)) ? $help_section['content'] : '';
            
            if ($help_section_key == 'side_tab') {
                if (is_callable(array($screen, 'set_help_sidebar')))
                    $screen->set_help_sidebar($content);
            } else {
                $screen->add_help_tab(array(
                    'id'      => $help_section_key, 
                    'title'   => __($title, $this->owner->getName()), 
                    'content' => __($content, $this->owner->getName()),
                ));
            }
        }
    }
}