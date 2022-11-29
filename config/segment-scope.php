<?php

/**
 * The 'pages' config use the path segments to determine the scope of the page.
 * In this case, we need to skip the segment which not part of scope (normally the dynamic value like parameter).
 * This config file is used determine the segment remove from path segments in specific case.
 *
 * You can add a case array in the array indexed with the module name to specify the path segment remove.
 * The case array must contain three elements:
 * 1. case: the case of the path segment
 * 2. position: the position of case in the path segment
 * 3. remove_segment: the segment index(s) to remove, this can be a number or an array of numbers
 *
 * For example:
 *
 * module: user
 *
 * path: "user/{user_id}/role/{role_id}/view"
 * scope: "user.role.view"
 *
 * You need to remove the segment "user_id" and "role_id" when the segment[2] is role.
 *
 * path: "user/{user_id}/permission/{permission_id}/view"
 * scope: "user.role.view"
 *
 * You need to remove the segment "user_id" and "permission_id" when the segment[2] is permission.
 *
 * "user" => [
 *      [
 *          "case" => "role",
 *          "position" => 2,
 *          "remove_segment" => [1, 3]
 *     ],
 *      [
 *          "case" => "permission",
 *          "position" => 2,
 *          "remove_segment" => [1, 3]
 *     ],
 * ]
 *
 */

return [
];
