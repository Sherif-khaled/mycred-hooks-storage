<div class="tablenav top">
    <div class="alignleft actions bulkactions">
        <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
        <select name="action" id="bulk-action-selector-top">
            <option value="-1" selected="selected">Bulk Actions</option>
            <option value="trash">Delete</option>
        </select>

        <input type="submit" id="doaction" class="button action bulkactions-apply" value="Apply">
    </div>

    <div class="tablenav-pages">


        <span class="displaying-num"></span>
        <span class="pagination-links"><a class="first-page" title="Go to the first page" href="#">«</a>
			<a class="prev-page" title="Go to the previous page" href="#">‹</a>
			<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Select Page</label><input
                        class="current-page" id="current-page-selector" title="Current page" type="text" name="paged"
                        value="1" size="1"> of <span class="total-pages"></span></span>
			<a class="next-page" title="Go to the next page" href="#">›</a>
			<a class="last-page" title="Go to the last page" href="#">»</a></span>
    </div>
</div>

<table class="hook-table wp-list-table widefat fixed striped pages">
    <thead>
    <tr>
        <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
            <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
            <input id="cb-select-all-1" type="checkbox">
        </th>
        <th tabindex="-1" scope="col" id="activity-id" class="manage-column column-title sorted desc"><a href="#"><span>Activity ID</span><span
                        class="sorting-indicator"></span></a></th>

        <th tabindex="-1" scope="col" id="activity-name" class="manage-column column-title sorted desc"><a
                    href="#"><span>Activity Name</span><span class="sorting-indicator"></span></a></th>

        <th tabindex="-1" style="width:100px" scope="col" id="date" class="manage-column column-title sorted desc"><a
                    href="#"><span>Created At.</span><span class="sorting-indicator"></span></a></th>

    </tr>
    </thead>
    <tbody id="hook-table">

    </tbody>
</table>

<div class="tablenav bottom">
    <div class="alignleft actions bulkactions">
        <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
        <select name="action" id="bulk-action-selector-bottom">
            <option value="-1" selected="selected">Bulk Actions</option>
            <option value="trash">Delete</option>
        </select>

        <input type="submit" id="doaction" class="button action bulkactions-apply" value="Apply">
    </div>

    <div class="tablenav-pages">


        <span class="displaying-num"></span>
        <span class="pagination-links"><a class="first-page" title="Go to the first page" href="#">«</a>
			<a class="prev-page" title="Go to the previous page" href="#">‹</a>
			<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Select Page</label><input
                        class="current-page" id="current-page-selector" title="Current page" type="text" name="paged"
                        value="1" size="1"> of <span class="total-pages"></span></span>
			<a class="next-page" title="Go to the next page" href="#">›</a>
			<a class="last-page" title="Go to the last page" href="#">»</a></span>
    </div>
</div>

