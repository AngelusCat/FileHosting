<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OriginalNameUploadedFileRegex implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $symbols = [
            "слэш (/)", "обратный слэш (\)", ":", "*", "?", "двойная кавычка (\")", "< >", "|", "+", "пробел", "&",
            ";", "одинарная кавычка (')", "обратная кавычка (`)", "[]", "{}", "$", "{}", "^", "#", "%", "!"
        ];
        $characterClass = preg_quote("^\/:*?\"<>|+ &;$'`[]{}^()#%!", '/');
        $regexp = "/^[^" . $characterClass . "]{1,255}$/";
        if (!preg_match($regexp, $value->getClientOriginalName())) {
            $fail("Сайт не принимает файлы, у которых название содержит следующие символы: " . implode(' ', $symbols));
        }
    }
}
