namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'business_name' => fake()->company(),
            'created_by' => null,
            'trial_starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
        ];
    }
} 