<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\DrivingRoute;
use Illuminate\Database\Seeder;

class DrivingRouteSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->routes() as $routeData) {
            $points = $routeData['points'];
            unset($routeData['points']);

            $routeData['city_id'] = City::where('name', $routeData['city'])->value('id');

            $route = DrivingRoute::updateOrCreate(
                ['title' => $routeData['title']],
                $routeData,
            );

            $route->points()->delete();

            foreach ($points as $index => $point) {
                $route->points()->create([
                    'sort_order' => $index + 1,
                    'maneuver' => $this->maneuverForInstruction($point['instruction']),
                    'instruction' => $point['instruction'],
                    'lat' => $point['lat'] ?? null,
                    'lng' => $point['lng'] ?? null,
                    'distance_km' => $point['distance_km'],
                    'duration' => $point['duration'],
                ]);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function routes(): array
    {
        return [
            [
                'title' => 'Arnprior Test Route Map',
                'package_type' => 'g1',
                'city' => 'Arnprior',
                'province' => 'ON',
                'description' => 'Arnprior drive test route map',
                'route_duration_minutes' => 25,
                'route_length_km' => 12.34,
                'start_label' => 'Daniel Street South, Arnprior',
                'destination_label' => 'Division Street, Arnprior',
                'start_lat' => 45.4339,
                'start_lng' => -76.3567,
                'end_lat' => 45.4234,
                'end_lng' => -76.3644,
                'price' => 14.99,
                'is_active' => true,
                'points' => [
                    ['instruction' => 'Drive northwest.', 'distance_km' => 0.03, 'duration' => '1 min', 'lat' => 45.4341, 'lng' => -76.3570],
                    ['instruction' => 'Turn right onto Daniel Street North/CR 2.', 'distance_km' => 0.35, 'duration' => '1 min', 'lat' => 45.4370, 'lng' => -76.3554],
                    ['instruction' => 'Turn left onto Rock Lane West.', 'distance_km' => 0.11, 'duration' => '1 min', 'lat' => 45.4375, 'lng' => -76.3570],
                    ['instruction' => 'Turn left onto John Street North.', 'distance_km' => 0.01, 'duration' => '1 min', 'lat' => 45.4370, 'lng' => -76.3574],
                    ['instruction' => 'Drive southwest on John Street North.', 'distance_km' => 0.10, 'duration' => '1 min', 'lat' => 45.4362, 'lng' => -76.3580],
                    ['instruction' => 'Turn right onto Elgin Street West.', 'distance_km' => 0.26, 'duration' => '1 min', 'lat' => 45.4357, 'lng' => -76.3610],
                    ['instruction' => 'Bear right onto Harrington Street.', 'distance_km' => 0.21, 'duration' => '1 min', 'lat' => 45.4344, 'lng' => -76.3628],
                    ['instruction' => 'Turn right onto Victoria Street.', 'distance_km' => 0.07, 'duration' => '1 min', 'lat' => 45.4340, 'lng' => -76.3618],
                    ['instruction' => 'Drive east on Victoria Street.', 'distance_km' => 0.04, 'duration' => '1 min', 'lat' => 45.4341, 'lng' => -76.3610],
                    ['instruction' => 'Turn right onto Harriet Street.', 'distance_km' => 0.18, 'duration' => '1 min', 'lat' => 45.4326, 'lng' => -76.3608],
                    ['instruction' => 'Turn right onto Madawaska Street/CR 1. Continue on CR 1.', 'distance_km' => 0.59, 'duration' => '1 min', 'lat' => 45.4316, 'lng' => -76.3540],
                    ['instruction' => 'Turn left onto Vancourtland Street North.', 'distance_km' => 0.13, 'duration' => '1 min', 'lat' => 45.4328, 'lng' => -76.3533],
                    ['instruction' => 'Turn right onto William Street West.', 'distance_km' => 0.20, 'duration' => '1 min', 'lat' => 45.4335, 'lng' => -76.3558],
                    ['instruction' => 'Drive northwest on William Street West.', 'distance_km' => 0.20, 'duration' => '1 min', 'lat' => 45.4345, 'lng' => -76.3576],
                    ['instruction' => 'Turn left onto Division Street/CR 10. Continue on CR 10.', 'distance_km' => 2.06, 'duration' => '3 mins', 'lat' => 45.4250, 'lng' => -76.3700],
                    ['instruction' => 'Turn left onto Allan Drive.', 'distance_km' => 0.57, 'duration' => '2 mins', 'lat' => 45.4205, 'lng' => -76.3750],
                    ['instruction' => 'Drive south on Allan Drive.', 'distance_km' => 0.00, 'duration' => '1 min', 'lat' => 45.4202, 'lng' => -76.3751],
                    ['instruction' => 'Turn left to stay on Allan Drive.', 'distance_km' => 0.08, 'duration' => '1 min', 'lat' => 45.4198, 'lng' => -76.3742],
                    ['instruction' => 'Turn right onto Edey Street.', 'distance_km' => 0.30, 'duration' => '1 min', 'lat' => 45.4185, 'lng' => -76.3715],
                    ['instruction' => 'Turn right onto Daniel Street South/CR 2.', 'distance_km' => 0.58, 'duration' => '1 min', 'lat' => 45.4222, 'lng' => -76.3658],
                    ['instruction' => 'Turn right onto Baskin Drive West/CR 10. Continue on CR 10.', 'distance_km' => 2.05, 'duration' => '3 mins', 'lat' => 45.4148, 'lng' => -76.3850],
                    ['instruction' => 'Turn right onto Caruso Street.', 'distance_km' => 0.13, 'duration' => '1 min', 'lat' => 45.4138, 'lng' => -76.3837],
                    ['instruction' => 'Drive southeast on Caruso Street.', 'distance_km' => 0.07, 'duration' => '1 min', 'lat' => 45.4134, 'lng' => -76.3830],
                    ['instruction' => 'Turn left onto Norma Street South.', 'distance_km' => 0.16, 'duration' => '1 min', 'lat' => 45.4145, 'lng' => -76.3815],
                    ['instruction' => 'Turn left onto Alicia Street.', 'distance_km' => 0.20, 'duration' => '1 min', 'lat' => 45.4160, 'lng' => -76.3828],
                    ['instruction' => 'Turn left onto Division Street/CR 10.', 'distance_km' => 0.82, 'duration' => '1 min', 'lat' => 45.4218, 'lng' => -76.3730],
                    ['instruction' => 'Drive southwest on Division Street/CR 10. Continue on CR 10.', 'distance_km' => 1.39, 'duration' => '2 mins', 'lat' => 45.4125, 'lng' => -76.3900],
                    ['instruction' => 'Turn left onto Daniel Street South/CR 2. Continue on CR 2.', 'distance_km' => 1.29, 'duration' => '2 mins', 'lat' => 45.4245, 'lng' => -76.3650],
                    ['instruction' => 'Turn right.', 'distance_km' => 0.04, 'duration' => '1 min', 'lat' => 45.4242, 'lng' => -76.3644],
                    ['instruction' => 'Drive southeast.', 'distance_km' => 0.05, 'duration' => '1 min', 'lat' => 45.4238, 'lng' => -76.3639],
                    ['instruction' => 'Turn right.', 'distance_km' => 0.05, 'duration' => '1 min', 'lat' => 45.4235, 'lng' => -76.3642],
                    ['instruction' => 'Your destination is on the right.', 'distance_km' => 0.00, 'duration' => '1 min', 'lat' => 45.4234, 'lng' => -76.3644],
                ],
            ],
            [
                'title' => 'Bancroft Test Route Map',
                'package_type' => 'g2',
                'city' => 'Bancroft',
                'province' => 'ON',
                'description' => 'Bancroft drive test route map',
                'route_duration_minutes' => 11,
                'route_length_km' => 3.19,
                'start_label' => 'Hastings Street North, Bancroft',
                'destination_label' => 'Cleak Avenue, Bancroft',
                'start_lat' => 45.0566,
                'start_lng' => -77.8545,
                'end_lat' => 45.0640,
                'end_lng' => -77.8535,
                'price' => 14.99,
                'is_active' => true,
                'points' => [
                    ['instruction' => 'Drive west.', 'distance_km' => 0.07, 'duration' => '1 min', 'lat' => 45.0566, 'lng' => -77.8545],
                    ['instruction' => 'Turn left onto Hastings Street North/62.', 'distance_km' => 0.05, 'duration' => '1 min', 'lat' => 45.0561, 'lng' => -77.8552],
                    ['instruction' => 'Drive south on Hastings Street North/62.', 'distance_km' => 0.26, 'duration' => '1 min', 'lat' => 45.0539, 'lng' => -77.8560],
                    ['instruction' => 'Turn right onto Station Street.', 'distance_km' => 0.22, 'duration' => '1 min', 'lat' => 45.0537, 'lng' => -77.8589],
                    ['instruction' => 'Drive west on Station Street.', 'distance_km' => 0.23, 'duration' => '1 min', 'lat' => 45.0538, 'lng' => -77.8619],
                    ['instruction' => 'Turn right onto Monck Street/28.', 'distance_km' => 0.18, 'duration' => '1 min', 'lat' => 45.0550, 'lng' => -77.8632],
                    ['instruction' => 'Turn right.', 'distance_km' => 0.01, 'duration' => '1 min', 'lat' => 45.0552, 'lng' => -77.8629],
                    ['instruction' => 'Drive north.', 'distance_km' => 0.05, 'duration' => '1 min', 'lat' => 45.0558, 'lng' => -77.8627],
                    ['instruction' => 'Turn right.', 'distance_km' => 0.07, 'duration' => '1 min', 'lat' => 45.0559, 'lng' => -77.8616],
                    ['instruction' => 'Turn right onto Monck Street/28.', 'distance_km' => 0.12, 'duration' => '1 min', 'lat' => 45.0563, 'lng' => -77.8602],
                    ['instruction' => 'Turn left onto Bridge Street West.', 'distance_km' => 0.16, 'duration' => '1 min', 'lat' => 45.0573, 'lng' => -77.8585],
                    ['instruction' => 'Drive south on Bridge Street West.', 'distance_km' => 0.07, 'duration' => '1 min', 'lat' => 45.0566, 'lng' => -77.8580],
                    ['instruction' => 'Turn left onto Maple Street.', 'distance_km' => 0.40, 'duration' => '1 min', 'lat' => 45.0535, 'lng' => -77.8557],
                    ['instruction' => 'Turn left to stay on Maple Street.', 'distance_km' => 0.08, 'duration' => '1 min', 'lat' => 45.0541, 'lng' => -77.8549],
                    ['instruction' => 'Drive northeast on Maple Street.', 'distance_km' => 0.31, 'duration' => '1 min', 'lat' => 45.0562, 'lng' => -77.8520],
                    ['instruction' => 'Turn left onto Hastings Street North/62.', 'distance_km' => 0.10, 'duration' => '1 min', 'lat' => 45.0570, 'lng' => -77.8543],
                    ['instruction' => 'Turn right onto Flint Avenue.', 'distance_km' => 0.12, 'duration' => '1 min', 'lat' => 45.0580, 'lng' => -77.8534],
                    ['instruction' => 'Turn left onto Cleak Avenue.', 'distance_km' => 0.05, 'duration' => '1 min', 'lat' => 45.0586, 'lng' => -77.8540],
                    ['instruction' => 'Drive north on Cleak Avenue.', 'distance_km' => 0.59, 'duration' => '2 mins', 'lat' => 45.0638, 'lng' => -77.8527],
                    ['instruction' => 'Turn left.', 'distance_km' => 0.04, 'duration' => '1 min', 'lat' => 45.0640, 'lng' => -77.8534],
                    ['instruction' => 'Your destination is on the right.', 'distance_km' => 0.00, 'duration' => '1 min', 'lat' => 45.0640, 'lng' => -77.8535],
                ],
            ],
            [
                'title' => 'Karachi Star Gate Sample Route',
                'package_type' => 'g1',
                'city' => 'Karachi',
                'province' => 'Sindh',
                'description' => 'Star Gate to Jinnah International Airport sample route for live tracking.',
                'route_duration_minutes' => 8,
                'route_length_km' => 3.20,
                'start_label' => 'Star Gate, Karachi',
                'destination_label' => 'Jinnah International Airport, Karachi',
                'start_lat' => 24.8916,
                'start_lng' => 67.1546,
                'end_lat' => 24.9065,
                'end_lng' => 67.1608,
                'price' => 14.99,
                'is_active' => true,
                'points' => [
                    ['instruction' => 'Start from Star Gate on Shahrah-e-Faisal.', 'distance_km' => 0.30, 'duration' => '1 min', 'lat' => 24.8916, 'lng' => 67.1546],
                    ['instruction' => 'Continue northeast toward Airport Road.', 'distance_km' => 0.55, 'duration' => '1 min', 'lat' => 24.8941, 'lng' => 67.1570],
                    ['instruction' => 'Bear left toward the airport approach.', 'distance_km' => 0.45, 'duration' => '1 min', 'lat' => 24.8975, 'lng' => 67.1590],
                    ['instruction' => 'Continue on Airport Road.', 'distance_km' => 0.65, 'duration' => '2 mins', 'lat' => 24.9010, 'lng' => 67.1613],
                    ['instruction' => 'Keep right toward Jinnah Terminal.', 'distance_km' => 0.55, 'duration' => '1 min', 'lat' => 24.9039, 'lng' => 67.1622],
                    ['instruction' => 'Turn left toward the terminal entrance.', 'distance_km' => 0.40, 'duration' => '1 min', 'lat' => 24.9054, 'lng' => 67.1614],
                    ['instruction' => 'Arrive at Jinnah International Airport.', 'distance_km' => 0.30, 'duration' => '1 min', 'lat' => 24.9065, 'lng' => 67.1608],
                ],
            ],
            [
                'title' => 'Clinton G Road Test Route',
                'package_type' => 'g1',
                'city' => 'Clinton',
                'province' => 'Ontario',
                'description' => 'Clinton G exit drive test route map prep.',
                'route_duration_minutes' => 15,
                'route_length_km' => 8.50,
                'start_label' => 'Clinton DriveTest Centre',
                'destination_label' => 'Vanastra Rd 3 Point Turn',
                'start_lat' => 43.6157000,
                'start_lng' => -81.5408000,
                'end_lat' => 43.5780000,
                'end_lng' => -81.5600000,
                'price' => 0.00,
                'is_active' => true,
                'points' => [
                    ['instruction' => 'Turn right onto Beech St toward Mill St.', 'distance_km' => 0.12, 'duration' => '1 min', 'lat' => 43.6157, 'lng' => -81.5408],
                    ['instruction' => 'Turn left onto Albert St.', 'distance_km' => 0.25, 'duration' => '1 min', 'lat' => 43.6149, 'lng' => -81.5420],
                    ['instruction' => 'Turn right onto Highway 8.', 'distance_km' => 0.10, 'duration' => '1 min', 'lat' => 43.6135, 'lng' => -81.5445],
                    ['instruction' => 'Turn right onto William St N.', 'distance_km' => 0.11, 'duration' => '1 min', 'lat' => 43.6128, 'lng' => -81.5452],
                    ['instruction' => 'Turn right onto Rattenbury St E.', 'distance_km' => 0.14, 'duration' => '1 min', 'lat' => 43.6118, 'lng' => -81.5455],
                    ['instruction' => 'Turn right onto Gibbings St.', 'distance_km' => 0.09, 'duration' => '1 min', 'lat' => 43.6112, 'lng' => -81.5440],
                    ['instruction' => 'Turn right onto Highway 8.', 'distance_km' => 0.32, 'duration' => '1 min', 'lat' => 43.6108, 'lng' => -81.5430],
                    ['instruction' => 'Turn left onto Victoria St / London Rd.', 'distance_km' => 1.60, 'duration' => '2 mins', 'lat' => 43.6080, 'lng' => -81.5420],
                    ['instruction' => 'Continue on London Rd (50-60-80 km/h).', 'distance_km' => 2.00, 'duration' => '2 mins', 'lat' => 43.5950, 'lng' => -81.5510],
                    ['instruction' => 'Turn right onto Vanastra Rd.', 'distance_km' => 0.08, 'duration' => '1 min', 'lat' => 43.5780, 'lng' => -81.5600],
                    ['instruction' => 'Perform 3-point turn and return.', 'distance_km' => 2.00, 'duration' => '2 mins', 'lat' => 43.5775, 'lng' => -81.5605],
                    ['instruction' => 'Return to London Rd toward DriveTest.', 'distance_km' => 2.20, 'duration' => '3 mins', 'lat' => 43.5950, 'lng' => -81.5510],
                    ['instruction' => 'Turn right onto Mill St.', 'distance_km' => 0.20, 'duration' => '1 min', 'lat' => 43.6140, 'lng' => -81.5415],
                    ['instruction' => 'Arrive at Clinton DriveTest Centre.', 'distance_km' => 0.00, 'duration' => '1 min', 'lat' => 43.6157, 'lng' => -81.5408],
                ],
            ],
        ];
    }

    private function maneuverForInstruction(string $instruction): string
    {
        if (str_starts_with($instruction, 'Turn left')) {
            return 'turn_left';
        }

        if (str_starts_with($instruction, 'Turn right') || str_starts_with($instruction, 'Bear right')) {
            return 'turn_right';
        }

        return 'continue';
    }
}
