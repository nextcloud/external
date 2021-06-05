<?php
/**
 * @copyright Copyright (c) 2012 Frank Karlitschek <frank@karlitschek.de>
 *
 * @author Frank Karlitschek <frank@karlitschek.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

script('external', 'external');
style('external', 'style');

/** @var array $_ */
?>
<iframe id="ifm" src="<?php p($_['url']); ?>" allow="camera; microphone; fullscreen; display-capture"></iframe>
