<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_pedigree\GetEntries;
use kalanis\kw_pedigree\Interfaces;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class MessageForm
 * @package KWCMS\modules\Pedigree\Lib
 * @property Controls\Select fatherId
 * @property Controls\Select motherId
 * @property Controls\Submit postRecord
 * @property Controls\Reset clearRecord
 */
class MessageForm extends Form
{
    /** @var GetEntries|null */
    protected $entry = null;

    /**
     * @param GetEntries $entry
     * @param string $targetHelper
     * @return MessageForm
     * @throws MapperException
     * @throws RuleException
     */
    public function composeForm(GetEntries $entry, string $targetHelper): self
    {
        $this->entry = $entry;
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addHidden('helperTargetLink', $targetHelper, ['id' => 'helper_target_link']);
        $this->addText($entry->getStorage()->getNameKey(), Lang::get('pedigree.text.name'), $entry->getStorage()->getName())
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $this->addText($entry->getStorage()->getFamilyKey(), Lang::get('pedigree.text.family'), $entry->getStorage()->getFamily());
        $this->addText($entry->getStorage()->getBirthKey(), Lang::get('pedigree.text.birth_date'), $entry->getStorage()->getBirth())
            ->addRule(IRules::SATISFIES_CALLBACK, Lang::get('warn.must_fill'), [$this, 'matchBirthDate']);
        $this->addText($entry->getStorage()->getTrialsKey(), Lang::get('pedigree.text.trials'), $entry->getStorage()->getTrials());
        $this->addRadios($entry->getStorage()->getSexKey(), Lang::get('pedigree.text.sex'), $entry->getStorage()->getSex(), [
            Interfaces\IEntry::SEX_MALE => Lang::get('pedigree.text.male'),
            Interfaces\IEntry::SEX_FEMALE => Lang::get('pedigree.text.female'),
        ])
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $this->addTextarea($entry->getStorage()->getTextKey(), Lang::get('pedigree.text.long_info'), $entry->getStorage()->getText(), [
            'cols' => 60, 'rows' => 4,
        ]);
        $fathers = $entry->getBySex(Interfaces\IEntry::SEX_MALE, Lang::get('pedigree.no_one'));
        $this->addSelect('fatherId', Lang::get('pedigree.text.father'), $entry->getStorage()->getFatherId(),
            array_combine(array_map([$this, 'getRecordKey'], $fathers), array_map([$this, 'getRecordName'], $fathers)), [
                'id' => 'father_select',
            ]
        );
        $mothers = $entry->getBySex(Interfaces\IEntry::SEX_FEMALE, Lang::get('pedigree.no_one'));
        $this->addSelect('motherId', Lang::get('pedigree.text.mother'), $entry->getStorage()->getMotherId(),
            array_combine(array_map([$this, 'getRecordKey'], $mothers), array_map([$this, 'getRecordName'], $mothers)), [
                'id' => 'mother_select',
            ]
        );
        $this->addSubmit('postRecord', Lang::get('dashboard.button_set'));
        $this->addReset('clearRecord', Lang::get('dashboard.button_reset'));
        return $this;
    }

    /**
     * @param ARecord $record
     * @return string
     * @throws MapperException
     */
    public function getRecordKey(ARecord $record): string
    {
        return strval($record->offsetGet($this->entry->getStorage()->getKeyKey()));
    }

    /**
     * @param ARecord $record
     * @return string
     * @throws MapperException
     */
    public function getRecordName(ARecord $record): string
    {
        return strval($record->offsetGet($this->entry->getStorage()->getNameKey())) . ' ' . strval($record->offsetGet($this->entry->getStorage()->getFamilyKey()));
    }

    public function matchBirthDate($value): bool
    {
        return intval(preg_match('#^([0-2][0-9]{3})-(0[0-9]|1[0-2])-([0-2][0-9]|3[0-1])$#', $value));
    }

    /**
     * @return MessageForm
     * @throws RuleException
     */
    public function addIdentifier(): self
    {
        $ident = $this->addText($this->entry->getStorage()->getKeyKey(), Lang::get('pedigree.text.key'), $this->entry->getStorage()->getKey());
        $ident->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $ident->addRule(IRules::SATISFIES_CALLBACK, Lang::get('pedigree.warn.already_exists'), [$this, 'checkKey']);
        $ident->addRule(IRules::SATISFIES_CALLBACK, Lang::get('pedigree.warn.contains_bad_chars'), [$this, 'checkChars']);
        return $this;
    }

    /**
     * @param $value
     * @return bool
     * @throws MapperException
     */
    public function checkKey($value): bool
    {
        return empty($this->entry->getByKey($value)->offsetGet($this->entry->getStorage()->getNameKey()));
    }

    public function checkChars($value): bool
    {
        return empty(preg_replace('#[a-zA-Z0-9_-]#', '', $value));
    }

    public function getDefaultRecord(): ?GetEntries
    {
        return $this->entry;
    }
}
