<?php

namespace kalanis\kw_auth_forms;


use ArrayAccess;
use kalanis\kw_forms\Form;
use kalanis\kw_rules\Exceptions\RuleException;
use kalanis\kw_rules\Rules\MatchAny;
use kalanis\kw_rules\Rules\ProcessCallback;


/**
 * Class AuthForm
 * @package kalanis\kw_auth_forms
 * How it works:
 * Insert this one into the form and call it:
 *
 *    new AuthForm('digest2', new Methods\ImplodeHash($currentUserCertLib), $this, ['first', 'next', 'last'], $cookies)
 *
 * It adds hidden input which will be checked for token value or digest code
 * Then when the form will be processed this hidden input allow/deny processing further because it checks for code in added input
 */
class AuthForm
{
    /**
     * @param string $inputAlias
     * @param Interfaces\IMethod $digestMethod
     * @param Form $boundForm
     * @param array $whichInputs
     * @param ArrayAccess $cookies
     * @throws RuleException
     */
    public function __construct(string $inputAlias, Interfaces\IMethod $digestMethod, Form $boundForm, array $whichInputs, ArrayAccess $cookies)
    {
        // init input
        $csrf = new Inputs\AuthCsrf();
        $csrf->setHidden($inputAlias, $cookies);

        // check for classical CSRF token
        $check = new ProcessCallback();
        $check->setAgainstValue([$csrf, 'checkToken']);
        $check->setErrorText('Token fails');

        // check content for digested value
        $digest = new Rules\CalcDigest($digestMethod);
        $digest->setAgainstValue($this->sentInputs($boundForm, $whichInputs));
        $digest->setErrorText('Digest fails');

        // match any rule
        $match = new MatchAny();
        $match->setErrorText('Nothing match');
        $match->setAgainstValue([$check, $digest]);

        // add rules to input
        $csrf->removeRules();
        $csrf->addRules([$match]);
        $boundForm->addControl($csrf);
    }

    protected function sentInputs(Form $sourceForm, array $whichInputs)
    {
        // we want only predefined ones
        $data = array_filter($sourceForm->getValues(), function ($k) use ($whichInputs) {
            return in_array($k, $whichInputs);
        }, ARRAY_FILTER_USE_KEY);

        // now set it in predefined order
        $flippedInputs = array_flip($whichInputs);
        uksort($data, function ($a, $b) use ($flippedInputs) {
            return strval($flippedInputs[$a]) > strval($flippedInputs[$b]) ? -1 : 1;
        });

        return $data;
    }
}
