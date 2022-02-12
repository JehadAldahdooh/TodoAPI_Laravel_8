<?php

namespace App\Interfaces;

interface TodoRepositoryInterface 
{
    public function show($status,$user); 
    public function create(array $taskDetails,$user);
    public function update($taskId,array $taskDetails,$user); 
    public function destroy($taskId,$user); 
}
