<?php
/**
 * Created by PhpStorm.
 * User: thilan
 * Date: 10/8/18
 * Time: 11:28 PM
 */

interface UserService
{

    public function findUser($id);

    public function findAllUsers();

    public function findAllUsersByRole($roleId);

    public function saveUser(Request $request);

    public function updateUser(Request $request);

    public function deleteUser($id);

}