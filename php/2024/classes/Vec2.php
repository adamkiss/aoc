<?php

class Vec2
{
    public function __construct(
        public int $x,
        public int $y
    ) {
    }

    public function cl_a(Vec2 $v): Vec2
    {
        return (clone $this)->add($v);
    }
    public function add(Vec2 $v): Vec2
    {
        $this->x += $v->x;
        $this->y += $v->y;
        return $this;
    }

    public function cl_ai(int $x, int $y): Vec2
    {
        return (clone $this)->addi($x, $y);
    }
    public function addi(int $x, int $y): Vec2
    {
        $this->x += $x;
        $this->y += $y;
        return $this;
    }

    public function cl_s(Vec2 $v): Vec2
    {
        return (clone $this)->sub($v);
    }
    public function sub(Vec2 $v): Vec2
    {
        $this->x -= $v->x;
        $this->y -= $v->y;
        return $this;
    }

    public function cl_si(int $x, int $y): Vec2
    {
        return (clone $this)->subi($x, $y);
    }
    public function subi(int $x, int $y): Vec2
    {
        $this->x -= $x;
        $this->y -= $y;
        return $this;
    }

    public function cl_muli(int $m, ?int $my = null): Vec2
    {
        return (clone $this)->muli($m, $my);
    }
    public function muli(int $m, ?int $my = null): Vec2
    {
        $this->x *= $m;
        $this->y += is_null($my) ? $m : $my;
        return $this;
    }
}
