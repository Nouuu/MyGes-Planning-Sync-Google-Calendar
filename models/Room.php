<?php


class Room
{
    public int $room_id;
    public string $name;
    public string $floor;
    public string $campus;
    public string $color;
    public string $latitude;
    public string $longitude;

    public function __construct()
    {
    }

    public static function fromObject($object): Room
    {
        $room = new Room();

        $room->room_id = empty($object->room_id) ? '' : $object->room_id;
        $room->name = empty($object->name) ? '' : $object->name;
        $room->floor = empty($object->floor) ? '' : $object->floor;
        $room->campus = empty($object->campus) ? '' : $object->campus;
        $room->color = empty($object->color) ? '' : $object->color;
        $room->latitude = empty($object->latitude) ? '' : $object->latitude;
        $room->longitude = empty($object->longitude) ? '' : $object->longitude;

        return $room;
    }
}