<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2021 Martin Adler <m.adler@familie-redlich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Fr\Typo3HandlebarsComponents\Domain\Factory\Page;

use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageFooterInterface;

/**
 * PageFooterFactoryInterface
 *
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
interface PageFooterFactoryInterface
{
    public function get(Page $page): PageFooterInterface;
}