<?php

use Illuminate\Database\Seeder;

class RoomsAddressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms_address')->delete();
    	
        DB::table('rooms_address')->insert([
['space_id' => '10001','address_line_1' => '227 Rue d\'Alésia','address_line_2' => '','city' => 'Paris','state' => 'Île-de-France','country' => 'FR','postal_code' => '75014','latitude' => '48.831919','longitude' => '2.3120169'],
  ['space_id' => '10002','address_line_1' => '','address_line_2' => '','city' => '','state' => '','country' => 'FR','postal_code' => '','latitude' => '46.227638','longitude' => '2.213749000000007'],
  ['space_id' => '10003','address_line_1' => '4 Villa Boileau','address_line_2' => '','city' => 'Paris','state' => 'Île-de-France','country' => 'FR','postal_code' => '75016','latitude' => '48.8457922','longitude' => '2.2636416'],
  ['space_id' => '10004','address_line_1' => '4561 Port Jersey Boulevard','address_line_2' => '','city' => 'Jersey City','state' => 'New Jersey','country' => 'US','postal_code' => '07305','latitude' => '40.6756029','longitude' => '-74.0811934'],
  ['space_id' => '10005','address_line_1' => '340-348 Oxford Street','address_line_2' => '','city' => 'London','state' => 'England','country' => 'GB','postal_code' => 'W1C 1JG','latitude' => '51.5150468','longitude' => '-0.14796409999997'],
  ['space_id' => '10006','address_line_1' => '17 Győri út','address_line_2' => '','city' => 'Budapest','state' => '','country' => 'HU','postal_code' => '1123','latitude' => '47.4914506','longitude' => '19.0266851'],
  ['space_id' => '10007','address_line_1' => '32Z Quai de Grenelle','address_line_2' => '','city' => 'Paris','state' => 'Île-de-France','country' => 'FR','postal_code' => '75015','latitude' => '48.8521807','longitude' => '2.2853453'],
  ['space_id' => '10008','address_line_1' => '2201-2271 Kaighns Avenue','address_line_2' => '','city' => 'Cherry Hill','state' => 'New Jersey','country' => 'US','postal_code' => '08002','latitude' => '39.9393509','longitude' => '-75.01542'],
  ['space_id' => '10009','address_line_1' => '5000 Estate Enighed','address_line_2' => '','city' => 'Independence','state' => 'Kansas','country' => 'US','postal_code' => '67301','latitude' => '37.0891604','longitude' => '-95.7131979'],
  ['space_id' => '10010','address_line_1' => '21 Carrer de Can Diviu','address_line_2' => '','city' => 'Sabadell','state' => 'Catalunya','country' => 'ES','postal_code' => '08205','latitude' => '41.5405','longitude' => '2.104'],
  ['space_id' => '10011','address_line_1' => '5219 Craven Court','address_line_2' => '','city' => 'Bensalem','state' => 'Pennsylvania','country' => 'US','postal_code' => '19020','latitude' => '40.1234759','longitude' => '-74.9314733'],
  ['space_id' => '10012','address_line_1' => '142 West 23rd Street','address_line_2' => '','city' => 'New York','state' => 'New York','country' => 'US','postal_code' => '10011','latitude' => '40.7434147','longitude' => '-73.9944533'],
  ['space_id' => '10013','address_line_1' => '2 Lichtensteinallee','address_line_2' => '','city' => 'Berlin','state' => 'Berlin','country' => 'DE','postal_code' => '10787','latitude' => '52.5104957','longitude' => '13.3447177'],
  ['space_id' => '10014','address_line_1' => 'Kona Highway','address_line_2' => '','city' => 'Kailua-Kona','state' => 'Hawaii','country' => 'US','postal_code' => '96740','latitude' => '19.7713938','longitude' => '-155.699914'],
  ['space_id' => '10015','address_line_1' => '318 East 15th Street','address_line_2' => '','city' => 'New York','state' => 'New York','country' => 'US','postal_code' => '10003','latitude' => '40.732473','longitude' => '-73.98373'],
  ['space_id' => '10016','address_line_1' => '11 Speight Street','address_line_2' => '','city' => 'Christchurch','state' => 'Canterbury','country' => 'NZ','postal_code' => '8013','latitude' => '-43.5050799','longitude' => '172.6451353'],
  ['space_id' => '10017','address_line_1' => '','address_line_2' => '','city' => 'London','state' => 'England','country' => 'GB','postal_code' => '','latitude' => '51.5073509','longitude' => '-0.12775829999998223'],
  ['space_id' => '10018','address_line_1' => '','address_line_2' => '','city' => 'Chennai','state' => 'Tamil Nadu','country' => 'IN','postal_code' => '','latitude' => '13.0826802','longitude' => '80.27071840000008']
        	]);
    }
}

