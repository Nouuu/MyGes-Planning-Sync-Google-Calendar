<?php
require_once __DIR__ . '/Discipline.php';

class Course
{
    public int $reservation_id;
    public array $rooms;
    public string $type;
    public int $author;
    public int $create_date;
    public int $start_date;
    public int $end_date;
    public string $state;
    public string $comment;
    public string $name;
    public Discipline $discipline;
    public string $teacher;
    public string $promotion;

    public function __construct()
    {
    }

    public static function fromObject($object): Course
    {
        $course = new Course();

        $course->reservation_id = empty($object->reservation_id) ? '' : $object->reservation_id;
        $course->rooms = empty($object->rooms) ? [] : $object->rooms;
        $course->type = empty($object->type) ? '' : $object->type;
        $course->author = empty($object->author) ? '' : $object->author;
        $course->create_date = empty($object->create_date) ? : $object->create_date;
        $course->start_date = empty($object->start_date) ? '' : $object->start_date;
        $course->end_date = empty($object->end_date) ? '' : $object->end_date;
        $course->state = empty($object->state) ? '' : $object->state;
        $course->comment = empty($object->comment) ? '' : $object->comment;
        $course->name = empty($object->name) ? '' : $object->name;
        $course->discipline = empty($object->discipline) ? '' : Discipline::fromObject($object->discipline);
        $course->teacher = empty($object->teacher) ? '' : $object->teacher;
        $course->promotion = empty($object->promotion) ? '' : $object->promotion;

        return $course;
    }
}