<?php


class Discipline
{
    public string $name;
    public int $nb_students;
    public int $rc_id;
    public int $school_id;
    public int $student_group_id;
    public string $student_group_name;
    public string $teacher;
    public int $teacher_id;
    public string $trimester;
    public int $trimester_id;
    public int $year;

    public function __construct()
    {
    }

    public static function fromObject($object): Discipline
    {
        $discipline = new Discipline();

        $discipline->name = empty($object->name) ? '' : $object->name;
        $discipline->nb_students = empty($object->nb_students) ?: $object->nb_students;
        $discipline->rc_id = empty($object->rc_id) ?: $object->rc_id;
        $discipline->school_id = empty($object->school_id) ?: $object->school_id;
        $discipline->student_group_id = empty($object->student_group_id) ?: $object->student_group_id;
        $discipline->student_group_name = empty($object->student_group_name) ? '' : $object->student_group_name;
        $discipline->teacher = empty($object->teacher) ? '' : $object->teacher;
        $discipline->teacher_id = empty($object->teacher_id) ?: $object->teacher_id;
        $discipline->trimester = empty($object->trimester) ? '' : $object->trimester;
        $discipline->trimester_id = empty($object->trimester_id) ?: $object->trimester_id;
        $discipline->year = empty($object->year) ?: $object->year;
        return $discipline;
    }

}