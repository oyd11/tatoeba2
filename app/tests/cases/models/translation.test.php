<?php
App::import('Model', 'Translation');

class TranslationTestCase extends CakeTestCase {
    var $fixtures = array(
        'app.sentence',
        'app.user',
        'app.group',
        'app.country',
        'app.sentence_comment',
        'app.contribution',
        'app.sentences_list',
        'app.sentences_sentences_list',
        'app.wall',
        'app.wall_thread',
        'app.favorites_user',
        'app.tag',
        'app.tags_sentence',
        'app.language',
        'app.link',
        'app.sentence_annotation',
    );

    function startTest() {
        Configure::write('AutoTranscriptions.enabled', false);
        $this->Translation =& ClassRegistry::init('Translation');
    }

    function endTest() {
        unset($this->Translation);
        ClassRegistry::flush();
    }

    function testFindCheckAllFields() {
        $result = $this->Translation->find(5, array());
        $expected = array(
            'Translation' => array(
                array('Translation' => array(
                    'id' => "2",
                    'text' => "问题的根源是，在当今世界，愚人充满了自信，而智者充满了怀疑。",
                    'user_id' => "7",
                    'lang' => "cmn",
                    'hasaudio' => "no",
                    'correctness' => "0",
                    'script' => null,
                )),
            ),
            'IndirectTranslation' => array(
                array('Translation' => array(
                    'id' => "1",
                    'text' => "The fundamental cause of the problem is that in the modern world, idiots are full of confidence, while the intelligent are full of doubt.",
                    'user_id' => "7",
                    'lang' => "eng",
                    'hasaudio' => "no",
                    'correctness' => "0",
                    'script' => null,
                )),
                array('Translation' => array(
                    'id' => "4",
                    'text' => "La cause fondamentale du problème est que dans le monde moderne, les imbéciles sont plein d'assurance, alors que les gens intelligents sont pleins de doute.",
                    'user_id' => "7",
                    'lang' => "fra",
                    'hasaudio' => "no",
                    'correctness' => "0",
                    'script' => null,
                )),
            ),
        );
        $this->assertEqual($expected, $result);
    }

    function _assertFind($sentenceId, $langs, $expectedTranslationIds, $expectedIndirectTranslationIds) {
        $result = $this->Translation->find($sentenceId, $langs);
        $result = Set::classicExtract($result, '{}.{n}.Translation.id');
        $expected = array(
            'Translation' => $expectedTranslationIds,
            'IndirectTranslation' => $expectedIndirectTranslationIds,
        );
        $this->assertEqual($expected, $result);
    }

    function testFindCheckIds() {
        $this->_assertFind(1, array(), array(2, 4, 3), array(5, 6));
    }

    function testFindWithFilteredDirectTranslation() {
        $this->_assertFind(1, array('cmn'), array(2), array());
    }

    function testFindWithFilteredIndirectTranslation() {
        $this->_assertFind(1, array('jpn'), array(), array(6));
    }

    function testFindWithFilteredMultipleLang() {
        $this->_assertFind(1, array('spa', 'deu'), array(3), array(5));
    }

    function testFindWithoutTranslation() {
        $this->_assertFind(7, array(), array(), array());
    }
}
