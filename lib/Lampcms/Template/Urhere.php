<?php
/**
 *
 * License, TERMS and CONDITIONS
 *
 * This software is licensed under the GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * Please read the license here : http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * ATTRIBUTION REQUIRED
 * 4. All web pages generated by the use of this software, or at least
 *       the page that lists the recent questions (usually home page) must include
 *    a link to the http://www.lampcms.com and text of the link must indicate that
 *    the website's Questions/Answers functionality is powered by lampcms.com
 *    An example of acceptable link would be "Powered by <a href="http://www.lampcms.com">LampCMS</a>"
 *    The location of the link is not important, it can be in the footer of the page
 *    but it must not be hidden by style attributes
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This product includes GeoLite data created by MaxMind,
 *  available from http://www.maxmind.com/
 *
 *
 * @author     Dmitri Snytkine <cms@lampcms.com>
 * @copyright  2005-2012 (or current year) Dmitri Snytkine
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * @link       http://www.lampcms.com   Lampcms.com project
 * @version    Release: @package_version@
 *
 *
 */


namespace Lampcms\Template;

use Lampcms\LampcmsObject;
use Lampcms\Registry;

/**
 * Class for handling creation of tabbed navigation
 * and passing the name of the 'currently active' tab.
 * This will create the "You are here" effect - the current
 * 'active' tab will be styled as "active" while other like
 * regular tags.
 *
 * @author Dmitri Snytkine
 *
 */
class Urhere extends LampcmsObject
{

    /**
     * Array of vars that will be passed to template
     * we need to first set one as 'current'
     * and also translate the rest (the ones that do not
     * end with _c)
     *
     * @var array
     */
    protected $vars = array(
        'newest_c' => '',
        'voted_c' => '',
        'active_c' => '',
        'newest' => '@@Newest@@',
        'newest_t' => '@@Most recent questions@@',
        'voted' => '@@Most Voted@@',
        'voted_t' => '@@Questions with most votes@@');

    /**
     *
     * @param Registry $Registry
     */
    public function __construct(Registry $Registry)
    {
       // $this->Registry = $Registry;
    }


    /**
     *
     * @param string $tpl     name of template class file
     *
     * @param string $current name of tab that should
     *                        be set as current
     *
     * @param array  $vars    this can be used to pass array of replacement
     *                        vars to template. Values from this array will be merged
     *                        with template's own array and if same keys exist then values
     *                        from this $vars array will override the template's default vars
     *
     * @param null   $func
     *
     * @return
     * @internal param \Lampcms\Template\function $Callable $func if passed will be used
     *           by template as callback function. If you need to pass callback
     *           but not passing any array $vars then the right way to call
     *           this method is $obj->get('tmpSomeTemplate', 'someparam', null, $func)
     */
    public function get($tpl, $current = '', array $vars = null, $func = null)
    {

        $template = ('tplToptabs' === $tpl && (LAMPCMS_CATEGORIES & 3) ) ? 'tplToptabsWithCategory': $tpl;
        $aVars = $template::getVars();
        if (\array_key_exists($current . '_c', $aVars)) {
            $aVars[$current . '_c'] = '_current';
        }

        if ($vars) {
            $aVars = \array_merge($aVars, $vars);
        }


        if (null !== $func) {
            d('$func is not null');
        }

        return $template::parse($aVars, false, $func);
    }

}
