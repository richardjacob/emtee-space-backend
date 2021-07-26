<?php

use Illuminate\Database\Seeder;

class DisputeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('disputes')->delete();
        DB::table('dispute_documents')->delete();
        DB::table('dispute_messages')->delete();

        DB::table('disputes')->insert([
            array('id' => '1','reservation_id' => '10014','dispute_by' => 'Guest','user_id' => '10002','dispute_user_id' => '10004','subject' => 'My event dates has been changed','amount' => '5000','final_dispute_amount' => '4970','currency_code' => 'INR','payment_status' => NULL,'paymode' => NULL,'first_name' => NULL,'last_name' => NULL,'postal_code' => NULL,'country' => '','transaction_id' => NULL,'status' => 'Closed','admin_status' => 'Open','created_at' => '2019-10-22 10:42:24','updated_at' => '2019-10-22 11:01:01'),
            array('id' => '2','reservation_id' => '10015','dispute_by' => 'Guest','user_id' => '10004','dispute_user_id' => '10002','subject' => 'This is not the space I expected .','amount' => '190','final_dispute_amount' => NULL,'currency_code' => 'USD','payment_status' => NULL,'paymode' => NULL,'first_name' => NULL,'last_name' => NULL,'postal_code' => NULL,'country' => '','transaction_id' => NULL,'status' => 'Open','admin_status' => 'Open','created_at' => '2019-10-22 11:00:00','updated_at' => '2019-10-22 11:00:00'),
            array('id' => '3','reservation_id' => '10016','dispute_by' => 'Guest','user_id' => '10001','dispute_user_id' => '10004','subject' => 'So many restrictions , space is not as shown in the images','amount' => '170','final_dispute_amount' => NULL,'currency_code' => 'INR','payment_status' => NULL,'paymode' => NULL,'first_name' => NULL,'last_name' => NULL,'postal_code' => NULL,'country' => '','transaction_id' => NULL,'status' => 'Processing','admin_status' => 'Open','created_at' => '2019-10-22 11:07:24','updated_at' => '2019-10-22 11:08:45'),
        ]);
        
        DB::table('dispute_documents')->insert([
            array('id' => '1','dispute_id' => '1','file' => 'dispute_document_1571722644iSzd.jpeg','uploaded_by' => '10002','created_at' => '2019-10-22 10:42:24','updated_at' => '2019-10-22 10:42:24'),
            array('id' => '2','dispute_id' => '2','file' => 'dispute_document_1571722200oxDm.jpeg','uploaded_by' => '10004','created_at' => '2019-10-22 11:00:00','updated_at' => '2019-10-22 11:00:00'),
            array('id' => '3','dispute_id' => '3','file' => 'dispute_document_15652600646uvN.jpg','uploaded_by' => '10001','created_at' => '2019-10-22 11:07:24','updated_at' => '2019-10-22 11:07:24'),            
        ]);

        DB::table('dispute_messages')->insert([
            array('id' => '1','dispute_id' => '1','message_by' => 'Guest','message_for' => 'Host','user_from' => '10002','user_to' => '10004','message' => 'Sorry Trio , My event dates has be changed due to demand in sponsors . Thanks in advance .','amount' => '5000','currency_code' => 'INR','read' => '1','created_at' => '2019-10-22 10:42:24','updated_at' => '2019-10-22 11:01:02'),
            array('id' => '2','dispute_id' => '2','message_by' => 'Guest','message_for' => 'Host','user_from' => '10004','user_to' => '10002','message' => 'Hi Tony , I\'m sorry , this is not the space I expected . I have made a reservation by accident','amount' => '190','currency_code' => 'USD','read' => '0','created_at' => '2019-10-22 11:00:00','updated_at' => '2019-10-22 11:00:00'),
            array('id' => '3','dispute_id' => '1','message_by' => 'Host','message_for' => 'Guest','user_from' => '10004','user_to' => '10002','message' => 'No problem tony , your are always welcome.','amount' => NULL,'currency_code' => 'USD','read' => '0','created_at' => '2019-10-22 11:01:01','updated_at' => '2019-10-22 11:01:01'),
            array('id' => '4','dispute_id' => '3','message_by' => 'Guest','message_for' => 'Host','user_from' => '10001','user_to' => '10004','message' => 'I have attached the damaged space image , Kindly refer this .','amount' => '170','currency_code' => 'INR','read' => '1','created_at' => '2019-10-22 11:07:24','updated_at' => '2019-10-22 11:07:45'),
            array('id' => '5','dispute_id' => '3','message_by' => 'Host','message_for' => 'Guest','user_from' => '10004','user_to' => '10001','message' => 'Sorry for the inconvenience , I will do the needful . Please give me some time','amount' => NULL,'currency_code' => 'USD','read' => '1','created_at' => '2019-10-22 11:08:45','updated_at' => '2019-10-22 12:32:31'),
            array('id' => '6','dispute_id' => '3','message_by' => 'Guest','message_for' => 'Host','user_from' => '10001','user_to' => '10004','message' => 'Yeah Thanks a lot .','amount' => NULL,'currency_code' => 'INR','read' => '0','created_at' => '2019-10-22 12:32:51','updated_at' => '2019-10-22 12:32:51'),          
        ]);
    }
}