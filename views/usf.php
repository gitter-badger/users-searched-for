<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Array of objects containing pulled from DB
$searchedTerms = $this->getSearchedTerms();

?>

<div class="wrap">
    <h3>Statistics - what your users have searched for on your website</h3>
    <div id="usf-records">
        <input type="search" placeholder="Search" class="search" />
        <div class="sort btn-sort-by-name" data-sort="sort-by-name">Sort by User</div>
        <div class="sort btn-sort-by-searched-term" data-sort="sort-by-searched-term">Sort by Term</div>
        <div class="sort btn-sort-by-page" data-sort="sort-by-page">Sort by Page</div>
        <div class="sort btn-sort-by-ip" data-sort="sort-by-ip">Sort by IP</div>
        <div class="sort btn-sort-by-date" data-sort="sort-by-date">Sort by Date</div>

        <hr />

        <table class="usf-table">
            <tr class="usf-table-title">
                <td>No.</td>
                <td>Username</td>
                <td>Searched term</td>
                <td>Landing page</td>
                <td>IP</td>
                <td>Time & Date</td>
            </tr>
            <!-- IMPORTANT, class="list" have to be at tbody -->
            <tbody class="list">

            <?php

            foreach ($searchedTerms as $key => $obj) {
                echo "
                        <tr class='usf-table-body'>
                            <td>{$obj->record_id}</td>
                            <td class='sort-by-name'>{$this->getUserName($obj->user_id)}</td>
                            <td class='sort-by-searched-term'>{$obj->searched_term}</td>
                            <td class='sort-by-page'>
                                <a href='{$this->getPageUrl($obj->page_id)}' target='_blank' name='{$this->getPageName($obj->page_id)}'>
                                    {$this->getPageName($obj->page_id)}
                                </a>
                            </td>
                            <td class='sort-by-ip'>{$obj->user_ip}</td>
                            <td class='sort-by-date'>{$obj->timestamp}</td>
                        </tr>
                ";
            }

            ?>

            </tbody>
            <ul class="pagination"></ul>
        </table>
    </div>

    <hr />
    <h3>Help</h3>
    <ul>
        <h4>The records table</h4>
        <li><strong>Username</strong> - the username of the user who made the search. If it is not registered, it will show as <em>Visitor</em>.</li>
        <li><strong>Searched term</strong> - what your users have searched for on your website.</li>
        <li><strong>Landing Page</strong> - if the user found a page in the search result, it will be shown as <em>Landing Page</em>. Otherwise <em>No results</em> will be displayed.</li>
        <li><strong>IP</strong> - IP address. Coming soon: countries and cities.</li>
        <li><strong>Time & Date</strong> - the time and date when the search was conducted.</li>
    </ul>
    <ul>
        <h4>Sort buttons</h4>
        <li>The Search box along with the Sort buttons will help you find anything within the records table.</li>
    </ul>

    <hr />
    <small>Problems? Questions? Please e-mail <a href="mailto:hello@plainsight.ro?Subject=Question%20via%20Users%20Searched%20For%20WP%20Plugin">hello@plainsight.ro</a></small>

</div>
