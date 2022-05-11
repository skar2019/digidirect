<<<<<<< HEAD
<?php

/*
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Liquid\Tag;

/**
 * An if statement
 *
 * Example:
 *
 *     {% unless true %} YES {% else %} NO {% endunless %}
 *
 *     will return:
 *     NO
 */

class TagUnless extends TagIf
{
	protected function negateIfUnless($display)
	{
		return !$display;
	}
}
=======
<?php

/*
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Liquid\Tag;

/**
 * An if statement
 *
 * Example:
 *
 *     {% unless true %} YES {% else %} NO {% endunless %}
 *
 *     will return:
 *     NO
 */

class TagUnless extends TagIf
{
	protected function negateIfUnless($display)
	{
		return !$display;
	}
}
>>>>>>> ef33f39aea584f35dbded9165562df55202812ae
