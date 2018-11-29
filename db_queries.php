<?php

function sql_select_todo_details_for_user_list($user_id, $list_id) {
    if($list_id == 0) {
        return "SELECT t.id, t.text, t.due_date, t.category, la.access_level FROM tudus t, tudu_lists l, tudu_list_access la WHERE t.user_id = '$user_id' and t.user_id = la.user_id and la.list_id = l.id and l.title = 'personal' and t.completed_date is null ORDER BY t.due_date, t.created_date DESC LIMIT 0, 200";
    }
    else {
        return "SELECT t.id, t.text, t.due_date, t.category, la.access_level FROM tudus t, tudu_lists l, tudu_list_access la WHERE t.user_id = '$user_id' and t.user_id = la.user_id and la.list_id = l.id and l.id = '$list_id' and t.completed_date is null ORDER BY t.due_date, t.created_date DESC LIMIT 0, 200";
    }
}

function sql_select_lists_for_user($user_id) {
    return "SELECT l.id, l.title FROM tudu_lists l, tudu_list_access la WHERE l.id = la.list_id and la.user_id = '$user_id' ORDER BY l.title DESC LIMIT 0, 200;";
}

?>
