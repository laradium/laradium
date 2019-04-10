<?php

namespace Laradium\Laradium\Traits;


trait CrudEvent
{

    /**
     * @var
     */
    private $events;

    public function __construct()
    {
        $this->events = collect([]);


        parent::__construct();
    }

    /**
     * @param $name
     * @param \Closure $callable
     * @return $this
     */
    public function event($name, \Closure $callable)
    {
//        dd($this->events);
//        if (is_array($name)) {
//            foreach ($name as $event) {
//                $this->events->put($event, $callable);
//            }
//        } else {
//            $this->events->put($name, $callable);
//        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param $name
     * @param $request
     */
    public function fireEvent($name, $request)
    {
        if (is_array($name)) {
            foreach ($name as $eventName) {
                $event = $this->events->filter(function ($value, $key) use ($eventName) {
                    return $key === $eventName;
                })->first();

                if ($event) {
                    $event($this->getModel(), $request);
                }
            }
        } else {
            $event = $this->events->filter(function ($value, $key) use ($name) {
                return $key === $name;
            })->first();

            if ($event) {
                $event($this->getModel(), $request);
            }
        }
    }
}