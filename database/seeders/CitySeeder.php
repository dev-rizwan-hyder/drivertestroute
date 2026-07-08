<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\DrivingRoute;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->cities() as $cityData) {
            $city = City::updateOrCreate(
                ['name' => $cityData['name']],
                ['address' => $cityData['address']],
            );

            DrivingRoute::whereNull('city_id')
                ->where('city', $city->name)
                ->update(['city_id' => $city->id]);
        }
    }

    /**
     * @return array<int, array{name: string, address: string}>
     */
    private function cities(): array
    {
        return [
            ['name' => 'Bancroft', 'address' => '141 Hastings St N, Unit 2, Bancroft, K0L 1C0'],
            ['name' => 'Barrie', 'address' => '520 Bryne Dr, Unit 7, Barrie, L4N 9P6'],
            ['name' => 'Belleville', 'address' => '345 College St E, Unit 12, RR#6, Belleville, K8N 5S7'],
            ['name' => 'Blind River', 'address' => '110 Indiana Avenue, Blind River, P0R 1B0'],
            ['name' => 'Brampton', 'address' => '59 First Gulf Blvd, Unit 9, Brampton, L6W 4P9'],
            ['name' => 'Brantford', 'address' => '41 Morton Ave E, Unit 2, Brantford, N3R 2N6'],
            ['name' => 'Brockville', 'address' => '2211 Parkedale Ave, Brockville, K6V 6B2'],
            ['name' => 'Burlington', 'address' => 'Burlington Power Centre, 1250 Brant St, Unit 2, Burlington, L7P 1X8'],
            ['name' => 'Chatham', 'address' => '171 Keil Dr S, Unit 4-5, Chatham, N7M 3H3'],
            ['name' => 'Clinton', 'address' => '154 Beech St, Clinton, N0M 1L0'],
            ['name' => 'Collingwood', 'address' => 'ServiceOntario, 191 Hurontario St, Unit 6, Collingwood, L9Y 2M1'],
            ['name' => 'Cornwall', 'address' => '120 Tollgate Rd W, Cornwall, K6J 5M3'],
            ['name' => 'Dryden', 'address' => 'Golden Mile Plaza, 539 Government St, Unit 8, Dryden, P8N 2P6'],
            ['name' => 'Elliot Lake', 'address' => '25 Dunn Road, Elliot Lake, P5A 2R9'],
            ['name' => 'Espanola', 'address' => 'Espanola Mall, 800 Centre St, Unit 101D, Espanola, P5E 1J3'],
            ['name' => 'Fort Frances', 'address' => '533 Mowat Ave, Fort Frances, P9A 1Z1'],
            ['name' => 'Guelph', 'address' => '255 Woodlawn Rd W., Unit 106, Guelph, N1H 8J1'],
            ['name' => 'Hamilton', 'address' => '370 Kenora Ave N, Hamilton, L8E 2W2'],
            ['name' => 'Hawkesbury', 'address' => 'Hawkesbury Mall, 400 Spence Ave, Unit 19, Hawkesbury, K6A 2Y3'],
            ['name' => 'Hearst', 'address' => 'Claude Larose Recreation Centre, 34 Tenth St, Hearst, P0L 1N0'],
            ['name' => 'Huntsville', 'address' => '215 Main St W, 1st Floor, Huntsville, P1H 1Z0'],
            ['name' => 'Kapuskasing', 'address' => 'Model City Hall, 25 Brunetville Rd, Unit 56, Kapuskasing, P5N 2E9'],
            ['name' => 'Kenora', 'address' => 'Kenora Shoppers Mall, 534 Park St, Unit 2B, Kenora, P9N 1A1'],
            ['name' => 'Kingston', 'address' => '381 Select Dr, Units 1-5, Kingston, K7M 8R1'],
            ['name' => 'Kirkland Lake', 'address' => 'Kirkland Lake Shopping Centre, 150 Government Rd W, Kirkland Lake, P2N 2E9'],
            ['name' => 'Kitchener', 'address' => '1405 Ottawa St N, Unit 112, Kitchener, N2A 3Z1'],
            ['name' => 'Lindsay', 'address' => 'Lindsay Square Mall, 401 Kent St W, Unit 20, Lindsay, K9V 4Z1'],
            ['name' => 'London', 'address' => '4380 Wellington Rd S, London, N6E 2Z6'],
            ['name' => 'Marathon', 'address' => 'Zero-100 Motel, 37 Peninsula Rd, Marathon, P0T 2E0'],
            ['name' => 'Mississauga', 'address' => '255 Longside Drive (Boulevard), Mississauga, L5W 1L8'],
            ['name' => 'Moosonee', 'address' => 'Moosonee Curling Club, 1 Arena Rd, Moosonee, P0L 1Y0'],
            ['name' => 'New Liskeard', 'address' => 'Timiskaming Square, RR#2, Site 2-152, Unit 4A (Hwy 11b and 65 E), New Liskeard, P0J 1P0'],
            ['name' => 'Newmarket', 'address' => '320 Harry Walker Parkway S, Newmarket, L3Y 7B4'],
            ['name' => 'North Bay', 'address' => 'New North Bay Mall, 300 Lakeshore Dr, Unit 502, North Bay, P1A 3V2'],
            ['name' => 'Oakville', 'address' => '2370 Wyecroft Rd, Oakville, L6L 5L7'],
            ['name' => 'Orangeville', 'address' => '50 Fourth Ave, Orangeville, L9W 4P1'],
            ['name' => 'Orillia', 'address' => '404 Laclie St, Unit 3, Orillia, L3V 4P5'],
            ['name' => 'Oshawa', 'address' => 'Midtown Mall, 200 John St W, Oshawa, L1J 2B4'],
            ['name' => 'Ottawa Canotek', 'address' => '5303 Canotek Rd., Unit 14, Ottawa Canotek, K1J 9M1'],
            ['name' => 'Ottawa Walkley', 'address' => '1570 Walkley Rd, Ottawa Walkley, K1V 6P5'],
            ['name' => 'Owen Sound', 'address' => 'Spring Mount Business Park, 107 Jason St, RR#5, Unit 1, Owen Sound, N4K 5N7'],
            ['name' => 'Parry Sound', 'address' => 'The Kinsmen Club, 110 Parry Sound Dr, Parry Sound, P2A 2X4'],
            ['name' => 'Pembroke', 'address' => '513 Eganville Rd, Pembroke, K8A 4E6'],
            ['name' => 'Peterborough', 'address' => '724 Lansdowne St W, Peterborough, K9J 1Z2'],
            ['name' => 'Port Hope', 'address' => 'Port Hope Knights of Columbus Hall, 1 Elias St, Port Hope, L1A 2Y7'],
            ['name' => 'Renfrew', 'address' => '115 Plaunt St S, Renfrew, K7V 1M5'],
            ['name' => 'Sarnia', 'address' => '1362 Lambton Mall Rd, Suite 5, Sarnia, N7S 5A1'],
            ['name' => 'Sault Ste Marie', 'address' => 'Churchill Plaza, 150 Churchill Blvd, C15-16, Sault Ste Marie, P6A 3Z9'],
            ['name' => 'Simcoe', 'address' => '140 Queensway Dr E, Simcoe, N3Y 4Y7'],
            ['name' => 'Smiths Falls', 'address' => '283 Brockville St., Unit 3, Smiths Falls, K7A 4Z6'],
            ['name' => 'St Catharines', 'address' => 'Bunting Square, 285 Bunting Rd, Unit 1, St. Catharines, L2M 7T9'],
            ['name' => 'Stratford', 'address' => '59 Lorne Ave E, Unit 3, Stratford, N5A 6S4'],
            ['name' => 'Sudbury', 'address' => 'Montrose Mall, 782 LaSalle Blvd, Sudbury, P3A 4V4'],
            ['name' => 'Thunder Bay', 'address' => 'McIntyre Centre, 1186 Memorial Ave, Unit 2, Thunder Bay, P7B 5K5'],
            ['name' => 'Tillsonburg', 'address' => 'Tillson Ave Mall, 107 Concession St E, Tillsonburg, N4G 4W4'],
            ['name' => 'Timmins', 'address' => 'The Porcupine Mall, 4900 Highway 101 East, Unit 160, Timmins, P0N 1K0'],
            ['name' => 'Toronto Downsview', 'address' => 'Downsview Park, 37 Carl Hall Rd (Use back entrance)'],
            ['name' => 'Toronto Etobicoke', 'address' => 'Centennial Park Plaza, 5555 Eglinton Ave W, Unit E120-124, Toronto Etobicoke, M9C 5M1'],
            ['name' => 'Toronto Metro East', 'address' => 'Victoria Terrace Plaza, 1448 Lawrence Ave E, Unit 15, Toronto Metro East, M4A 2V6'],
            ['name' => 'Toronto Port Union', 'address' => 'The Village of Abbey Lane Shopping Centre, 91 Rylander Blvd, Unit 109A, Toronto Port Union, M1B 5M5'],
            ['name' => 'Walkerton', 'address' => 'Saugeen Business Park, 200 McNab St, Walkerton, N0G 2V0'],
            ['name' => 'Wawa', 'address' => 'Michipicoten Memorial Community Centre, 85-90 Chris Simon Dr, PO Box 500, Wawa, P0S 1K0'],
            ['name' => 'Windsor', 'address' => '2470 Dougall Ave, Windsor, N8X 1T2'],
            ['name' => 'Woodstock', 'address' => '476 Peel St, Woodstock, N4S 1K1'],
        ];
    }
}
