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


namespace Lampcms;


/**
 *
 * Class represents one question stored
 * in Mongo QUESTIONS collection
 * implements LampcmsResourceInterface
 *
 * @author Dmitri Snytkine
 *
 */
class Question extends \Lampcms\Mongo\Doc implements Interfaces\Question, Interfaces\UpDownRatable, Interfaces\CommentedResource
{

    /**
     * Currently not used, was going to have
     * method to get all answers for this question
     * by querying ansCollection
     * but so far this is not required.
     *
     * It may be required later when implementing
     * something like an NNTP server
     *
     * @var string
     */
    protected $ansCollection = 'ANSWERS';

    public function __construct(Registry $Registry, array $a = null)
    {

        $a = ($a) ? $a : array();
        parent::__construct($Registry, 'QUESTIONS', $a);
    }


    /**
     * (non-PHPdoc)
     *
     * @see LampcmsResourceInterface::getResourceTypeId()
     * @return string
     */
    public function getResourceTypeId()
    {
        return 'QUESTION';
    }


    /**
     * (non-PHPdoc)
     *
     * @see ResourceInterface::getResourceId()
     * @return array|bool|int|mixed|null
     */
    public function getResourceId()
    {

        return $this->offsetGet('_id');
    }


    /**
     * Convenience method so that it can be used from
     * objects that expect a Resource but not necessarily know
     * if Resource is going to be Question or Answer
     *
     * @todo Add Interface for this and implement it in Question
     *       and Answer
     *
     * (non-PHPdoc)
     * @see  Lampcms\Interfaces.Resource::getResourceId()
     * @return array|bool|int|mixed|null
     */
    public function getQuestionId()
    {
        return $this->getResourceId();
    }


    /**
     * (non-PHPdoc)
     *
     * @see LampcmsResourceInterface::getDeletedTime()
     * @return array|bool|int|mixed|null
     */
    public function getDeletedTime()
    {

        return $this->offsetGet('i_del_ts');
    }


    /**
     * (non-PHPdoc)
     *
     * @see LampcmsResourceInterface::getOwnerId()
     * @return int
     */
    public function getOwnerId()
    {

        return (int)$this->offsetGet('i_uid');
    }


    /**
     * (non-PHPdoc)
     *
     * @see LampcmsResourceInterface::getLastModified()
     * @return array|bool|int|mixed|null
     */
    public function getLastModified()
    {

        return $this->offsetGet('i_lm_ts');
    }


    /**
     * Get value of i_etag but if it does
     * not exist then return value of i_lm_ts
     *
     * @return int timestamp of last modification
     */
    public function getEtag()
    {
        $ret = $this->offsetGet('i_etag');

        return (!empty($ret)) ? $ret : $this->offsetGet('i_lm_ts');
    }


    /**
     * Get full (absolute) url for this question,
     * including the http and our domain
     *
     * @param bool $short if true then don't include the title slug in the url
     *
     * @return string url for this question
     */
    public function getUrl($short = false)
    {

        $url = '{_WEB_ROOT_}/{_viewquestion_}/{_QID_PREFIX_}'.$this->offsetGet('_id');
        if(!$short){
            $url .= '/'.$this->offsetGet('url');
        }

        $url = $this->getRegistry()->Ini->SITE_URL.$url;
        $callback = $this->Registry->Router->getCallback();

        $ret = $callback($url);

        return $ret;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.Post::getBody()
     * @return string body of question
     */
    public function getBody()
    {
        return $this->offsetGet('b');
    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.Post::getTitle()
     * @return string
     */
    public function getTitle()
    {
        return $this->offsetGet('title');
    }

    /**
     * @return int id of category
     */
    public function getCategoryId()
    {
        return $this->offsetGet('i_cat');
    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.Post::getSeoUrl()
     * @return string the slug for the seo
     */
    public function getSeoUrl()
    {
        return $this->offsetGet('url');
    }


    /**
     * Test to see if question is closed. If it is closed
     * then returns array of data that contains
     * Username, reason and time of when question was
     * closed
     *
     * @return mixed false if not closed | array of a_closed
     * if is closed
     */
    public function isClosed()
    {
        $a = $this->offsetGet('a_closed');

        return (empty($a)) ? false : $a;
    }


    /**
     * @return int number of answers this question has
     */
    public function getAnswerCount()
    {

        return $this->offsetGet('i_ans');
    }


    /**
     * Set time, reason for when question was closed
     * as well as username and userid of user who closed it
     *
     * @param \Lampcms\User|object $closer User who closed the question
     *
     * @param string               $reason
     *
     * @return object $this
     */
    public function setClosed(User $closer, $reason = null)
    {

        if ($reason) {
            $reason = \strip_tags((string)$reason);
        }

        if (!$this->offsetExists('a_closed')) {
            parent::offsetSet('a_closed', array(
                    'username' => $closer->getDisplayName(),
                    'i_uid'    => $closer->getUid(),
                    'av'       => $closer->getAvatarSrc(),
                    'reason'   => $reason,
                    'hts'      => date('F j, Y g:i a T')
                )
            );

        }

        return $this;
    }


    /**
     * Mark this item as deleted but only
     * if not already marked as deleted
     *
     * @param \Lampcms\User                     $user
     * @param string                            $reason optional reason for delete
     *
     * @internal param \Lampcms\User $object $user user marking this
     *           item as deleted
     *
     * @return object $this
     */
    public function setDeleted(User $user, $reason = null)
    {

        if (0 === $this->getDeletedTime()) {

            if ($reason) {
                $reason = \strip_tags((string)$reason);
            }

            parent::offsetSet('i_del_ts', time());
            parent::offsetSet('a_deleted',
                array(
                    'username' => $user->getDisplayName(),
                    'i_uid'    => $user->getUid(),
                    'av'       => $user->getAvatarSrc(),
                    'reason'   => $reason,
                    'hts'      => date('F j, Y g:i a T')
                )
            );
        }

        return $this;
    }


    /**
     *
     * Adds a_edited array of data to Question
     *
     * @param User   $user
     * @param string $reason reason for editing
     *
     * @return object $this
     */
    public function setEdited(User $user, $reason = '')
    {

        if (!empty($reason)) {
            $reason = \strip_tags((string)$reason);
        }

        $aEdited = $this->offsetGet('a_edited');
        if (empty($aEdited) || !is_array($aEdited)) {
            $aEdited = array();
        }

        $aEdited[] = array(
            'username' => $user->getDisplayName(),
            'i_uid'    => $user->getUid(),
            'av'       => $user->getAvatarSrc(),
            'reason'   => $reason,
            'i_ts'     => time(),
            'hts'      => date('F j, Y g:i a T'));

        parent::offsetSet('a_edited', $aEdited);

        return $this;
    }


    /**
     *
     * Set tags for this question
     * It will also update "a_edited" array
     * to record the retag action, records
     * user who retagged, and "Retag" as reason for edit
     * Will also update lastModified
     *
     * @param User  $user object User who retagged this question
     * @param array $tags array of tags
     *
     * @return \Lampcms\Question
     */
    public function retag(User $user, array $tags)
    {

        parent::offsetSet('a_tags', $tags);
        parent::offsetSet('tags_html', \tplQtags::loop($tags, false));

        $b = $this->offsetGet('b');
        d('b: ' . $b);

        $oHtmlParser = \Lampcms\String\HTMLStringParser::stringFactory(Utf8String::stringFactory($b, 'utf-8', true));
        $body        = $oHtmlParser->unhilight()->hilightWords($tags)->valueOf();

        $this->offsetSet('b', $body);

        $this->setEdited($user, 'Retagged')->touch();

        return $this;
    }


    /**
     * Sets the id of best_answer,
     * id of user that supplied best answer
     * sets 'status' to 'accptd'
     * and also updates the Answer object to the
     * set accepted property to true
     *
     * In case question was 'unanswered' we must also
     * update UNANSWERED_TAGS
     *
     * @param Answer $Answer object of type Answer represents
     *                       Answer being accepted as best answer
     *
     * @return \Lampcms\Question
     */
    public function setBestAnswer(Answer $Answer)
    {
        d('about to set status to accptd');
        parent::offsetSet('i_sel_ans', $Answer->getResourceId());
        parent::offsetSet('i_sel_uid', $Answer->getOwnerId());

        /**
         * Now set the Answer object's accepted status to true
         */
        $Answer->setAccepted()->touch();

        /**
         * If Question is still not 'answered', means
         * no accepted answer,
         * then since we are not changing its status
         * to answered, we must update
         * the count of unanswered tags, which
         * is done via UnansweredTags object
         */
        if ('accptd' !== $this->offsetGet('status')) {
            UnansweredTags::factory($this->Registry)->remove($this);
        }

        parent::offsetSet('status', 'accptd');
        d('setting status to accptd');

        $this->touch(false);

        return $this;
    }


    /**
     * Increases i_ans by $inc, which is usually 1
     * but can also be used to decrease the count, by
     * passing a negative value
     *
     * (non-PHPdoc)
     *
     * @see QuestionInterface::increaseAnswerCount()
     *
     * @param int $inc
     *
     * @throws \InvalidArgumentException
     * @return \Lampcms\Question
     */
    public function updateAnswerCount($inc = 1)
    {
        if (!\is_int($inc)) {
            throw new \InvalidArgumentException('Param $inc must be an integer. was: ' . gettype($inc));
        }

        $iAns = $this->offsetGet('i_ans');
        d('$iAns ' . $iAns);

        /**
         * Set new value of i_ans but make sure
         * it will never be less than 0
         * This is just an extra guard, should not
         * really happened, but passing a negative value
         * is possible when we need to decrease answer count,
         * that's why we need this guard here.
         */
        $newCount = max(0, ($iAns + $inc));
        d('$newCount: ' . $newCount);

        parent::offsetSet('i_ans', $newCount);

        /**
         * Change the status to answrd
         * 'answrd' is not the same as 'accptd'
         * it simply serves a purpose to set the style
         * of div to not be red, but it still does not
         * make the question 'answered'
         */
        if ($newCount < 1) {
            parent::offsetSet('status', 'unans');
        } elseif ('unans' === $this->offsetGet('status')) {
            parent::offsetSet('status', 'answrd');
        }

        /**
         * If new value is NOT 1 then set
         * a_s (plural suffix) to 's'
         */
        if (1 !== ($newCount)) {
            parent::offsetSet('ans_s', 's');
        } else {
            parent::offsetSet('ans_s', '');
        }

        return $this;
    }


    /**
     * Updates last modified timestamp
     *
     * @param bool $etagOnly
     *
     * @return object $this
     */
    public function touch($etagOnly = false)
    {
        $time = time();

        $this->offsetSet('i_etag', $time);
        if (!$etagOnly) {
            $this->offsetSet('i_lm_ts', $time);
        }

        return $this;
    }


    /**
     * Logic: For guests don't check question owner id
     * For others: insert into QUESTION_VIEWS first as a way
     * to test for duplicates.
     * Duplicates are: same uid and same qid
     * If no duplicate then also increase count of views for this
     * question
     *
     * @todo try to run this as post-echo method via runLater
     *       callback. This is not really resource intensive, but still...
     *       it checks for duplicate, checks viewer ID, etc...
     *       This also runs on every page view, and also since we use fsync when
     *       updating via MongoDoc object, it does require disk write.
     *
     *
     * @param \Lampcms\User                     $Viewer
     * @param int                               $inc
     *
     * @throws \InvalidArgumentException
     * @return object $this
     */
    public function increaseViews(\Lampcms\User $Viewer, $inc = 1)
    {
        if (!\is_int($inc)) {
            throw new \InvalidArgumentException('Param $inc must be an integer. was: ' . gettype($inc));
        }

        /**
         * @todo Don't count question owner view
         *       For this we must be able to get Viewer from Registry
         *
         * Filter out duplicate views
         */
        $viewerId = $Viewer->getUid();

        /**
         * If guest, then there
         * will be a problem if we at least don't check
         * for same session_id
         */
        $viewerId = (0 === $viewerId) ? session_id() : $viewerId;

        $ownerID = $this->offsetGet('i_uid');

        d('$viewerId: ' . $viewerId . ' $ownerID: ' . $ownerID);

        if ($viewerId === $ownerID) {
            d('viewing own question');

            return $this;
        }

        $iViews = $this->offsetGet('i_views');

        /**
         * If this is the first view, we will cheat a little
         * and set the views to 2
         * There will never be just 1 view, and this way we don't
         * have to worry about the plural suffix
         */
        if (0 === $iViews && (1 === $inc)) {
            $inc = 2;
        }

        $collViews = $this->getRegistry()->Mongo->QUESTION_VIEWS;
        $collViews->ensureIndex(array('uid' => 1,
                                      'qid' => 1), array('unique' => true));
        $qid = (int)$this->offsetGet('_id');
        try {
            $collViews->insert(array('qid'  => $qid,
                                     'uid'  => $viewerId,
                                     'i_ts' => time()), array('safe' => true));
            parent::offsetSet('i_views', ($iViews + (int)$inc));

            /**
             * If new value is NOT 1 then set
             * vw_s (plural suffix) to 's' otherwise
             * must set to empty string because
             * by default it's already set to 's'
             */
            $this->offsetSet('vw_s', 's');

        } catch ( \MongoException $e ) {
            d('duplicate view for qid ' . $qid . ' uid: ' . $viewerId);
        }

        return $this;
    }


    /**
     * Process an UP vote for this question
     *
     * @param int $inc could be 1 or -1
     *
     * @throws \InvalidArgumentException
     * @return \Lampcms\Question
     */
    public function addUpVote($inc = 1)
    {

        if ($inc !== 1 && $inc !== -1) {
            throw new \InvalidArgumentException('$inc can only be 1 or -1. Was: ' . $inc);
        }

        $tmp   = (int)$this->offsetGet('i_up');
        $score = (int)$this->offsetGet('i_votes');
        $total = ($score + $inc);

        parent::offsetSet('i_up', max(0, ($tmp + $inc)));
        parent::offsetSet('i_votes', $total);

        /**
         * Plural extension handling
         */
        $v_s = (1 === abs($total)) ? '' : 's';
        parent::offsetSet('v_s', $v_s);

        return $this;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.UpDownRatable::addDownVote()
     *
     * @param int $inc
     *
     * @throws \InvalidArgumentException
     * @return \Lampcms\Question
     */
    public function addDownVote($inc = 1)
    {

        if ($inc !== 1 && $inc !== -1) {
            throw new \InvalidArgumentException('$inc can only be 1 or -1. Was: ' . $inc);
        }

        $tmp   = (int)$this->offsetGet('i_down');
        $score = (int)$this->offsetGet('i_votes');
        $total = ($score - $inc);

        parent::offsetSet('i_down', max(0, ($tmp + $inc)));
        /**
         * Question can have negative score, so we allow it!
         */
        parent::offsetSet('i_votes', $total);

        /**
         * Plural extension handling
         */
        $v_s = (1 === abs($total)) ? '' : 's';
        parent::offsetSet('v_s', $v_s);

        return $this;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.UpDownRatable::getVotesArray()
     * @return array
     */
    public function getVotesArray()
    {

        $a = array(
            'up'    => $this->offsetGet('i_up'),
            'down'  => $this->offsetGet('i_down'),
            'score' => $this->offsetGet('i_votes'));

        return $a;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.UpDownRatable::getScore()
     * @return array|bool|int|mixed|null
     */
    public function getScore()
    {
        return $this->offsetGet('i_votes');
    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.CommentedResource::addComment()
     *
     * @param \Lampcms\CommentParser $Comment
     *
     * @return \Lampcms\Question
     */
    public function addComment(CommentParser $Comment)
    {
        $aKeys = array(
            '_id',
            'i_uid',
            'i_prnt',
            'username',
            'avtr',
            'b_owner',
            'inreplyto',
            's_inreply',
            'b',
            't',
            'i_ts',
            'cc',
            'cn',
            'reg',
            'city',
            'zip',
            'lat',
            'lon'
        );

        $aComments = $this->getComments();
        d('aComments: ' . print_r($aComments, 1));
        /**
         * Only keep the keys that we need
         * get rid of keys like hash, i_res
         * because we don't need them here
         */
        $aComment = $Comment->getArrayCopy();
        $aComment = array_intersect_key($aComment, array_flip($aKeys));

        $aComments[] = $aComment;

        $this->setComments($aComments);

        /**
         * A commentor on the question
         * is considered a question contributor,
         * so we must add contributor now
         */
        $this->addContributor($aComment['i_uid']);

        return $this;

    }


    /**
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.CommentedResource::getCommentsCount()
     * @return int
     */
    public function getCommentsCount()
    {
        $aComments = $this->getComments();

        return count($aComments);
    }


    /**
     * Increase value of i_commets by 1
     *
     * @param int $count
     *
     * @throws \InvalidArgumentException
     * @return object $this
     */
    public function increaseCommentsCount($count = 1)
    {
        if (!is_int($count)) {
            throw new \InvalidArgumentException('$count must be integer. was: ' . gettype($count));
        }
        /**
         * Now increase comments count
         */
        $commentsCount = $this->getCommentsCount();
        d('$commentsCount ' . $commentsCount);

        parent::offsetSet('i_comments', ($commentsCount + $count));

        return $this;
    }


    /**
     * Remove one comment from array of comments
     * then re-save the new array of comments
     * the numerical keys of array will be reset
     * Also i_comments value will be updated to the
     * new count of comments
     *
     * (non-PHPdoc)
     *
     * @see Lampcms\Interfaces.CommentedResource::deleteComment()
     *
     * @param $id
     *
     * @return \Lampcms\Question
     */
    public function deleteComment($id)
    {

        if (0 === $this->getCommentsCount()) {
            d('This question does not have any comments');

            return $this;
        }

        $aComments = $this->getComments();

        for ($i = 0; $i < count($aComments); $i += 1) {
            if ($aComments[$i]['_id'] == $id) {
                d('unsetting comment: ' . $i);
                array_splice($aComments, $i, 1);
                break;
            }
        }

        $newCount = count($aComments);
        if (0 === $newCount) {
            $this->offsetUnset('a_comments');
        } else {
            $this->setComments($aComments);
        }

        parent::offsetSet('i_comments', $newCount);

        return $this;
    }


    /**
     * Add userid of User to the list
     * of contributors.
     * A Contributor is anyone who
     * has made an answer or a comment
     * to a question
     *
     * Contributors array is not unique,
     * it can have more than one entry for
     * the same user if user contributed multiple
     * times. This way we can remove just one record
     * and user is still considered a contributor
     * as long as the same user has contributed other items
     *
     * @param $User
     *
     * @throws \InvalidArgumentException
     * @internal param int|object $mixed $User object of type User
     * @return \Lampcms\Question
     */
    public function addContributor($User)
    {
        if (!\is_int($User) && (!\is_object($User) || !($User instanceof User))) {
            throw new \InvalidArgumentException('Value of $User can be only int or instance of User class. it was: ' . var_export($User, true));
        }

        $uid = (\is_int($User)) ? $User : $User->getUid();
        $a   = $this->offsetGet('a_uids');
        $a[] = $uid;

        parent::offsetSet('a_uids', $a);

        return $this;
    }


    /**
     * Remove user id of User $User
     * from array of contributors
     * Contributors array is not unique,
     * it can have more than one entry for
     * the same user if user contributed multiple
     * times. This way we can remove just one record
     * and user is still considered a contributor
     * as long as the same user has contributed other items
     *
     * @param $User $User
     *
     * @throws \InvalidArgumentException
     * @return \Lampcms\Question
     */
    public function removeContributor($User)
    {

        if (!is_int($User) && (!is_object($User) || !($User instanceof User))) {
            throw new \InvalidArgumentException('Value of $User can be only int or instance of User class. it was: ' . var_export($User, true));
        }

        $changed = false;
        $uid     = (\is_int($User)) ? $User : $User->getUid();
        $a       = $this->offsetGet('a_uids');
        for ($i = 0; $i < count($a); $i += 1) {
            if ($uid == $a[$i]) {
                d('unsetting contributor: ' . $uid . ' at array key: ' . $i);
                \array_splice($a, $i, 1);
                $changed = true;
                break;
            }
        }

        if ($changed) {
            $this->offsetSet('a_uids', $a);
        }

        return $this;
    }


    /**
     * Add userID of user to the array
     * of a_flwrs
     *
     * @param mixed $User int|object of type User
     *
     * @throws \InvalidArgumentException
     * if $User is not int and not a User object
     *
     * @return object $this
     */
    public function addFollower($User)
    {
        if (!is_int($User) && (!is_object($User) || !($User instanceof \Lampcms\User))) {
            throw new \InvalidArgumentException('param $User can be integer or object of type User. Was: ' . var_export($User, true));
        }

        $uid = (is_int($User)) ? $User : $User->getUid();

        $aFollowers = $this->offsetGet('a_flwrs');
        if (!in_array($uid, $aFollowers)) {
            $aFollowers[] = $uid;
            $this->offsetSet('a_flwrs', $aFollowers);
            $this->save();
        }

        return $this;
    }


    /**
     * Remove userID of user from the array
     * of a_flwrs
     *
     * @param mixed $User int|object of type User
     *
     * @throws \InvalidArgumentException
     * if $User is not int and not a User object
     *
     * @return object $this
     */
    public function removeFollower($User)
    {
        if (!is_int($User) && (!is_object($User) || !($User instanceof \Lampcms\User))) {
            throw new \InvalidArgumentException('param $User can be integer or object of type User. Was: ' . var_export($User, true));
        }

        $uid = (is_int($User)) ? $User : $User->getUid();

        $aFollowers = $this->offsetGet('a_flwrs');
        if (false !== $key = array_search($uid, $aFollowers)) {
            d('cp unsetting key: ' . $key);
            array_splice($aFollowers, $key, 1);
            $this->offsetSet('a_flwrs', $aFollowers);
            $this->save();
        }

        return $this;
    }


    /**
     * Sets value of lp_u : a link to Last Poster profile
     * and lp_t a time of last post
     *
     * @todo should make the last answerer an array
     *       and then just push the value there
     *       This way if answer is deleted we can just delete
     *       that one element from array!
     *
     * @param User                                  $User object of type User who made the last
     *                                                    Answer or Comment to this question
     *
     * @param \Lampcms\Answer                       $Answer
     *
     * @return object $this
     */
    public function setLatestAnswer(User $User, Answer $Answer)
    {
        $aLatest = $this->offsetGet('a_latest');
        $a       = array(
            'u'  => '<a href="' . $User->getProfileUrl() . '">' . $User->getDisplayName() . '</a>',
            't'  => date('F j, Y g:i a T', $Answer->getLastModified()),
            'id' => $Answer->getResourceId()
        );

        /**
         * Latest answer data goes
         * to top of array
         */
        \array_unshift($aLatest, $a);

        $this->offsetSet('a_latest', $aLatest);

        return $this;
    }


    /**
     * Removes one element from a_latest array
     * that represents answer passed in param.
     *
     * If that array had only one element
     * then also unset the whole 'a_latest' key
     * from this object
     *
     * @param \Lampcms\Answer|object $Answer object of type Answer
     *
     * @return object $this
     */
    public function removeAnswer(Answer $Answer)
    {
        $id      = $Answer->getResourceId();
        $aLatest = $this->offsetGet('a_latest');

        for ($i = 0; $i < count($aLatest); $i += 1) {
            if (!empty($aLatest[$i]) && ($id === $aLatest[$i]['id'])) {
                \array_splice($aLatest, $i, 1);
                break;
            }
        }

        if (0 === count($aLatest)) {
            $this->offsetUnset('a_latest');
        } else {
            parent::offsetSet('a_latest', $aLatest);
        }

        /**
         * If removed Answer was also a "accepted" answer
         * then change status to just "answrd" here
         *
         * The updateAnswerCount(-1) method
         * may then change the status to "unans"
         * if it's determined that this was
         * the only answer
         *
         * Also need to add this question to
         * UNANSWERED_TAGS again because now
         * this question is technically unanswered again
         */
        if ((true === $Answer['accepted']) &&
            ($id === $this->offsetGet('i_sel_ans'))
        ) {
            parent::offsetSet('status', 'answrd');
            $this->offsetUnset('i_sel_ans');
            $this->offsetUnset('i_sel_uid');
            UnansweredTags::factory($this->Registry)->set($this);
        }

        $this->updateAnswerCount(-1)
            ->removeContributor($Answer->getOwnerId());

        $this->touch(false);

        return $this;
    }


    /**
     * Getter for 'comments' element
     *
     * @return array of comments or empty array if
     * 'comments' element not present in the object
     *
     */
    public function getComments()
    {
        return $this->offsetGet('a_comments');
    }


    /**
     * Get one comment from
     * a_comments array
     *
     * @param int $id comment id
     *
     * @throws DevException if param $id is not an integer
     *
     * @return mixed array of one comment | false if comment not found by $id
     *
     */
    public function getComment($id)
    {
        if (!\is_int($id)) {
            throw new DevException('param $id must be integer. Was: ' . $id);
        }

        $aComments = $this->getComments();

        for ($i = 0; $i < count($aComments); $i += 1) {
            if ($id == $aComments[$i]['_id']) {
                return $aComments[$i];
            }
        }

        return false;
    }


    /**
     * Sets the 'a_comments' key via parent::offsetSet
     * Using parent because offsetSet of this class
     * will disallow setting a_comments key directly!
     *
     *
     * @param array $aComments comments array
     *
     * @return object $this
     */
    public function setComments(array $aComments)
    {
        parent::offsetSet('a_comments', $aComments);
        parent::offsetSet('i_comments', count($aComments));

        return $this;
    }


    /**
     * Get id of question asker
     *
     * @return int id of user who asked (owner) of the question
     */
    public function getQuestionOwnerId()
    {
        return $this->getOwnerId();
    }


    /**
     * Get username of asker
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->offsetGet('username');
    }

    /**
     * This method prevents setting some
     * values directly
     *
     * (non-PHPdoc)
     *
     * @see ArrayObject::offsetSet()
     *
     * @param mixed $index
     * @param mixed $newval
     *
     * @throws DevException
     */
    public function offsetSet($index, $newval)
    {
        switch ( $index ) {

            case 'i_comments':
                throw new DevException('value of i_comments cannot be set directly. Use increaseCommentsCount() method');
                break;

            case 'i_down':
            case 'i_up':
            case 'i_votes':
                throw new DevException('value of ' . $index . ' keys cannot be set directly. Use addDownVote or addUpVote to add votes');
                break;

            case 'a_deleted':
            case 'i_del_ts':
                throw new DevException('value of ' . $index . ' cannot be set directly. Must use setDeleted() method for that');
                break;

            case 'i_ans':
                throw new DevException('value of i_ans cannot be set directly. Use updateAnswerCount() method');
                break;

            case 'i_views':
                throw new DevException('value of i_ans cannot be set directly. Use increaseViews() method');
                break;

            case 'a_edited':
                throw new DevException('value of a_edited cannot be set directly. Must use setEdited() method for that');
                break;

            case 'a_closed':
                throw new DevException('value of a_closed cannot be set directly. Must use setClosed() method for that');
                break;

            case 'comments':
            case 'a_comments':
                throw new DevException('value of a_comments cannot be set directly. Must use setComments() method for that');
                break;

            /*case 'a_latest':
                    throw new DevException('value of a_latest cannot be set directly. Must use setLatestAnswer() method for that');
                    break;*/

            case 'i_sel_uid':
            case 'i_sel_ans':
                throw new DevException('value of ' . $index . ' cannot be set directly. Must use setBestAnswer() method for that');
                break;

            default:
                parent::offsetSet($index, $newval);
        }
    }
}
