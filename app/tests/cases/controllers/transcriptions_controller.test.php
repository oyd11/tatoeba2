<?php
/* Transcriptions Test cases generated on: 2014-10-20 00:25:43 : 1413764743*/
App::import('Controller', 'Transcriptions');

App::import('Component', 'Cookie');
Mock::generate('CookieComponent');

App::import('Component', 'Auth');
Mock::generate('AuthComponent');

class TestTranscriptionsController extends TranscriptionsController {
    function beforeFilter() {
        /* Replace the CookieComponent with a mock in order to prevent
           the 'headers already sent' error when a cookie is written.
        */
        $this->Cookie =& new MockCookieComponent();

        /* Replace the AuthComponent to easily log anyone. */
        $this->Auth =& new MockAuthComponent();
        if ($this->params['loggedInUserForTest']) {
            $user = $this->params['loggedInUserForTest'];
            $this->Auth->setReturnValue('user', $user);
            unset($this->params['loggedInUserForTest']);
        }

        parent::beforeFilter();
    }

    function redirect() {
        /* Avoid redirecting for real since it causes the good old
           'Cannot modify header information' error. */
    }
}

class TranscriptionsControllerTestCase extends CakeTestCase {
    var $fixtures = array(
        'app.aro',
        'app.aco',
        'app.aros_aco',
        'app.favorites_user',
        'app.language',
        'app.link',
        'app.sentence',
        'app.sentence_annotation',
        'app.sentences_sentences_list',
        'app.tag',
        'app.tags_sentence',
        'app.transcription',
        'app.wall',
        'app.wall_thread',
    );

    function setUp() {
        Configure::write('Acl.database', 'test_suite');
    }

    function startTest() {
        /* avoid stupid warnings in beforeFilter */
        global $_SERVER;
        $_SERVER['REQUEST_URI'] = '';

        $this->Transcriptions =& new TestTranscriptionsController();
        $this->Transcriptions->constructClasses();
        $this->User = ClassRegistry::init('User');
    }

    function endTest() {
        unset($this->Transcriptions);
        unset($this->User);
    }

    function _editAsUser($username, $transcrId, $transcrText) {
        $user = $this->User->find('first', array(
            'conditions' => array('username' => $username),
            'recursive' => -1,
        ));
        $data = array('id' => $transcrId, 'value' => $transcrText);

        return $this->testAction(
            '/jpn/transcriptions/edit',
            array(
                'form' => $data,
                'method' => 'post',
                'controller' => 'TestTranscriptions',
                'loggedInUserForTest' => $user,
            )
        );
    }

    function testGuestCantEditTranscription() {
        $result = $this->_editAsUser(null, 1, 'something new');
        $this->assertFalse($result);
    }
    function testOwnerCanEditTranscription() {
        $result = $this->_editAsUser('kazuki', 1, 'something new');
        $this->assertTrue($result);
    }

    function testRegularOtherUserCantEditTranscription() {
        $result = $this->_editAsUser('contributor', 1, 'something new');
        $this->assertFalse($result);
    }

    function testAdminCanEditTranscription() {
        $result = $this->_editAsUser('admin', 1, 'something new');
        $this->assertTrue($result);
    }

    function testCorpusMaintainerCanEditTranscription() {
        $result = $this->_editAsUser('corpus_maintainer', 1, 'something new');
        $this->assertTrue($result);
    }

    function testAdvancedUserCantEditTranscription() {
        $result = $this->_editAsUser('advanced_contributor', 1, 'something new');
        $this->assertFalse($result);
    }
}