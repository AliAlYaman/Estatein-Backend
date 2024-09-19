<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => $this->faker->randomElement(['assets/cards/hotel.png' , 'assets/cards/hotel-2.png' , 'assets/cards/hotel-3.png']),
            'title' => $this->faker->randomElement(['Luxury Villa' , 'Modern Apartment']),
            'subtitle'=> $this->faker->randomElement(['A stunning 4-bedroom, 3-bathroom villa in a peaceful suburban neighborhood' , "A chic and fully-furnished 2-bedroom apartment with panoramic city views", 'An elegant 3-bedroom, 2.5-bathroom townhouse in a gated community']),
            'bedroom' => $this->faker->randomElement(['1-bedroom' , '3-bedroom' , '4-bedroom' ,'6-bedroom' , '8-bedroom']),
            'bathroom' => $this->faker->randomElement(['1-bathroom' , '3-bathroom' , '4-bathroom' ,'6-bathroom' , '8-bathroom']),
            'type' => 'Villa',
            'price' => '$750,000'
        ];
    }
}
