<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Controller for activities (i.e. things that contributors can do in Tatoeba).
 *
 * @category Activities
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ActivitiesController extends AppController
{
    public $components = array ('CommonSentence');
    public $uses = array('Sentence', 'Tag', 'User');

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array("*");
    }


    /**
     * Add new sentences.
     *
     * @return void
     */
    public function add_sentences()
    {
    }


    /**
     * Adopt sentences.
     *
     * @param string $lang
     */
    public function adopt_sentences($lang = null)
    {
        $this->helpers[] = 'CommonModules';
        $this->helpers[] = 'Pagination';

        $conditions = array('user_id' => null);
        if(!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        $this->loadModel('Sentence');
        $this->paginate = array(
            'limit' => CurrentUser::getSetting('sentences_per_page'),
            'conditions' => $conditions,
            'contain' => array()
        );
        $results = $this->paginate('Sentence');
        $this->set('results', $results);
        $this->set('lang', $lang);
    }


    /**
     * Improve sentences.
     */
    public function improve_sentences()
    {
        $tagChangeName = $this->Tag->getChangeTagName();
        $tagCheckName = $this->Tag->getCheckTagName();
        $tagDeleteName = $this->Tag->getDeleteTagName();
        $tagNeedsNativeCheckName = $this->Tag->getNeedsNativeCheckTagName();
        $tagOKName = $this->Tag->getOKTagName();

        $tagChangeId = $this->Tag->getIdFromName($tagChangeName);
        $tagCheckId = $this->Tag->getIdFromName($tagCheckName);
        $tagDeleteId = $this->Tag->getIdFromName($tagDeleteName);
        $tagNeedsNativeCheckId = $this->Tag->getIdFromName($tagNeedsNativeCheckName);
        $tagOKId = $this->Tag->getIdFromName($tagOKName);

        $this->set('tagChangeName', $tagChangeName);
        $this->set('tagCheckName', $tagCheckName);
        $this->set('tagDeleteName', $tagDeleteName);
        $this->set('tagNeedsNativeCheckName', $tagNeedsNativeCheckName);
        $this->set('tagOKName', $tagOKName);
      
        $this->set('tagChangeId', $tagChangeId);
        $this->set('tagCheckId', $tagCheckId);
        $this->set('tagDeleteId', $tagDeleteId);
        $this->set('tagNeedsNativeCheckId', $tagNeedsNativeCheckId);
        $this->set('tagOKId', $tagOKId);
    }


    /**
     * Translate sentences.
     */
    public function translate_sentences()
    {
        $this->helpers[] = 'Languages';

        if (isset($_GET['langFrom']) && isset($_GET['langTo']))
        {
            $langFrom = Sanitize::paranoid($_GET['langFrom']);
            $langTo = Sanitize::paranoid($_GET['langTo']);

            $this->redirect(
                array(
                    "controller" => "sentences",
                    "action" => "show_all_in",
                    /* REVISIT!!! */
                    $langFrom, 'none', $langTo
                )
            );
        }
    }


    /**
     * Translate sentences of a specific user.
     *
     * @param string $username          Username.
     * @param string $lang              Language of the sentences.
     */
    public function translate_sentences_of($username, $lang = null) {
        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'Languages';
        $this->helpers[] = 'CommonModules';

        $this->set('username', $username);

        $userId = $this->User->getIdFromUsername($username);

        if (empty($userId)) {
            $flashMessage = format(
                __("There's no user called {username}", true),
                array('username' => $username)
            );
            $this->Session->setFlash($flashMessage);
            $this->redirect(
                array(
                    'controller' => 'users',
                    'action' => 'all'
                )
            );
        }

        $this->loadModel('Sentence');

        $conditions = array(
            'user_id' => $userId
        );
        if (!empty($lang)) {
            $conditions['lang'] = $lang;
        }

        $this->paginate = array(
            'Sentence' => array(
                'fields' => array(
                    'id',
                ),
                'conditions' => $conditions,
                'contain' => array(),
                'limit' => CurrentUser::getSetting('sentences_per_page'),
                'order' => 'created DESC'
            )
        );

        $paginationResults = $this->paginate('Sentence');

        $sentenceIds = array();

        foreach ($paginationResults as $i=>$sentence) {
            $sentenceIds[$i] = $sentence['Sentence']['id'];
        }

        $results = $this->CommonSentence->getAllNeededForSentences(
            $sentenceIds
        );

        $this->set('results', $results);
        $this->set('lang', $lang);
    }
}
?>
