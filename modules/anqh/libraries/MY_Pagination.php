<?php
/**
 * Anqh extended Pagination library.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Pagination extends Pagination_Core {

	/**
	 * Go to last page
	 */
	public function to_last_page() {
		$this->current_page       = $this->total_pages;
		$this->current_first_item = (int) min((($this->current_page - 1) * $this->items_per_page) + 1, $this->total_items);
		$this->current_last_item  = (int) min($this->current_first_item + $this->items_per_page - 1, $this->total_items);

		// If there is no first/last/previous/next page, relative to the
		// current page, value is set to FALSE. Valid page number otherwise.
		$this->first_page         = ($this->current_page === 1) ? FALSE : 1;
		$this->last_page          = false;
		$this->previous_page      = ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
		$this->next_page          = false;
	}

}
