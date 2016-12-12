<?php
/**
 * Created by PhpStorm.
 * User: mbouc
 * Date: 13-Jun-16
 * Time: 12:24 PM
 */

namespace Mcms\Pages\Services\PageCategory;

use Mcms\Pages\Exceptions\InvalidPageCategoryFormatException;
use Validator;

class PageValidator
{
    public function validate(array $item)
    {
        $check = Validator::make($item, [
            'title' => 'required',
            'user_id' => 'required',
            'active' => 'required',
        ]);

        if ($check->fails()) {
            throw new InvalidPageCategoryFormatException($check->errors());
        }

        return true;
    }
}