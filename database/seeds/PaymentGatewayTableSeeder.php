<?php

use Illuminate\Database\Seeder;

class PaymentGatewayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_gateway')->delete();

        DB::table('payment_gateway')->insert([
    		['name' => 'username', 'value' => 'pvignesh90-facilitator_api1.gmail.com', 'site' => 'PayPal'],
    		['name' => 'password', 'value' => '1381798304', 'site' => 'PayPal'],
    		['name' => 'signature', 'value' => 'AiPC9BjkCyDFQXbSkoZcgqH3hpacALfsdnEmmarK-6V7JsbXFL2.hoZ8', 'site' => 'PayPal'],
    		['name' => 'mode', 'value' => 'sandbox', 'site' => 'PayPal'],
            ['name' => 'client', 'value' => 'ASeeaUVlKXDd8DegCNSuO413fePRLrlzZKdGE_RwrWqJOVVbTNJb6-_r6xX9GdsRUVNc8butjTOIK_Xm', 'site' => 'PayPal'],
            ['name' => 'secret', 'value' => 'ENCGBUb_QSpHzGIAxjtSehkRIAI9lOELOiZUUjZUTEdjACeILOUUG58ijBNsuzdV-RPyDbHNxYTPkapn', 'site' => 'PayPal'],

            ['name' => 'publish', 'value' => 'pk_test_MaI2vc3u6b9cYO7QaROLqmxD00Dq1I55Ee', 'site' => 'Stripe'],
            ['name' => 'secret', 'value' => 'sk_test_kzV4SvexWbEpyuMvbGEmBrjB006i1jFBBN', 'site' => 'Stripe'],
            ['name' => 'client_id', 'value' => 'ca_ForOV7VbMfNHBviIpDhJaOGOpl84hNda', 'site' => 'Stripe'],
    	]);
    }
}
