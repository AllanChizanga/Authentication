<?php

namespace App\DTOs\User;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;

class UpdateUserData extends Data
{
    public function __construct(
        #[Sometimes, StringType, Max(255)]
        public ?string $fullname,
        
        #[Sometimes, Email, Max(255)]
        public ?string $email,

        #[Sometimes, StringType, Max(20)]
        public ?string $national_id,

        #[Sometimes, StringType, Max(15)]
        public ?string $phone,

        #[Sometimes, StringType, Max(255)]
        public ?string $country,

        #[Sometimes, StringType, Max(255)]
        public ?string $city,   

        #[Sometimes, StringType, Max(255)]
        public ?string $profile_photo,  

        #[Sometimes, StringType, Max(255)]
        public ?string $id_photo,       

        #[Sometimes, StringType, Max(255)]
        public ?string $work_location,
        #[Sometimes, StringType, Max(255)]
        public ?string $home_location,
        #[Sometimes, StringType, Max(50)]
        public ?string $gender, 
        #[Sometimes, StringType, Max(50)]
        public ?string $payment_preference,
        #[Sometimes, StringType, Max(10)]
        public ?string $is_activated,
        #[Sometimes, StringType, Max(50)]
        public ?string $badge,
        #[Sometimes, StringType, Max(255)]
        public ?string $password,
        #[Sometimes]
        public ?string $created_at,
        #[Sometimes]
        public ?string $updated_at,

    ) {}
}