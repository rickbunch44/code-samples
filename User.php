<?php

namespace App;

use Faker\Generator;
use Faker\Provider\Person;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;

class User extends \Illuminate\Foundation\Auth\User
{
    use Notifiable;

    const DEMO_USER = 'eae867f3-32b7-4980-b2d2-b66ad453cf27';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // TODO: FOR SEEDING ONLY
        $faker = \Faker\Factory::create();
        $fn = $faker->firstName;
        $ln = $faker->lastName;

        $this->attributes['uuid'] = Uuid::uuid4()->toString();
        $this->attributes['fname'] = $fn;
        $this->attributes['lname'] = $ln;
        $this->attributes['email'] = substr($fn, 0, 1) . $ln . '@pingtheworld.us';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname', 'lname', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'salt',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
