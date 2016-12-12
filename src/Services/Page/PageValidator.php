<?php
/**
 * Created by PhpStorm.
 * User: mbouc
 * Date: 13-Jun-16
 * Time: 12:24 PM
 */

namespace Mcms\Pages\Services\Page;

use Mcms\Pages\Exceptions\InvalidPageFormatException;
use Validator;

class PageValidator
{
    public function validate(array $item)
    {
        $check = Validator::make($item, [
            'title' => 'required',
            'user_id' => 'required',
            'active' => 'required',
            'categories' => 'required|array',
        ]);

        if ($check->fails()) {
            throw new InvalidPageFormatException($check->errors());
        }

        return true;
    }
}