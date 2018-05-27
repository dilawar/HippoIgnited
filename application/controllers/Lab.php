<?php

require_once BASEPATH. 'autoload.php';

function checkEquipmentBookingRequest( array &$request )
{
    $errorMsg = '';
    foreach( array("equipment_id", "date", "start_time", "end_time") as $k )
        if( ! __get__($request, $k, null) )
            $errorMsg .= "Error: No $k is selected. <br />";

    if( isDateAndTimeIsInPast( $request['date'], $request['start_time'] ) )
    {
        $errorMsg .= 'You are trying to book in the past. Date ' . $request['date'] 
            . ' and time ' . $request['start_time'] . '.';
    }
    return $errorMsg;
}

function checkEquipmentMultibookingRequest( array &$request )
{
    $errorMsg = '';
    foreach( array("equipment_id", "day_pattern", "start_time", "end_time") as $k )
        if( ! __get__($request, $k, null) )
            $errorMsg .= "Error: No $k is selected. <br />";

    return $errorMsg;
}


trait Lab 
{
    // VIEWS
    public function equipments( )
    {
        $this->load_user_view( "user_manages_equipments");
    }

    public function browse_equipments( )
    {
        $this->load_user_view( "user_browse_equipments" );
    }

    public function add_equipment( $arg = '' )
    {
        $_POST['edited_by'] = whoAmI();
        $_POST['last_modified_on'] = dbDateTime( 'now' );

        $personInCharge = $_POST['person_in_charge'];
        if( ! findAnyoneWithEmail( $personInCharge) )
        {
            printWarning( "I could not locate <tt>PERSON IN CHARGE</tt> '$personInCharge' in my
                database. I won't allow this entry. Use a valid email." 
                );
            redirect( "user/equipments");
            return;
        }

        $updatable =  'name,vendor,description,last_modified_on,edited_by,status,person_in_charge';
        $res = insertOrUpdateTable('equipments', 'id,faculty_in_charge,'.$updatable
            , $updatable, $_POST);
        if( !$res )
            echo printWarning( "Failed to add equipment.");
        else
            flashMessage( "Successfully added equipment." );
        redirect( "user/equipments" );
    }

    // ACTION
    public function delete_equipment( $equipmentID )
    {
        if( $_POST['response'] == 'DO_NOTHING' )
        {
            flashMessage( "User cancelled previous action." );
            redirect( "user/equipments");
            return;
        }

        $res = deleteFromTable( "equipments", 'id', array('id' => $equipmentID));
        if( $res )
            flashMessage( "Successfully deleted equipment id $equipmentID." );
        else
            printWarning( "Failed to delete equipment id $equipmentID.");

        redirect( "user/equipments");
    }

    public function book_equipment( )
    {
        $errorMsg = checkEquipmentBookingRequest( $_POST );
        if( $errorMsg )
        {
            printWarning( $errorMsg );
            redirect( 'user/browse_equipments');
            return;
        }

        // Everything is fine. Just book it.
        $res = insertIntoTable( 'equipment_bookings'
                , 'id,equipment_id,date,start_time,end_time,booked_by,comment'
                , $_POST 
            );

        if($res)
            flashMessage( "Booked succesfully but I did not send any email.");

        redirect( "user/browse_equipments");
    }

    public function multibook_equipment()
    {
        $errorMsg = checkEquipmentMultibookingRequest( $_POST );
        if( $errorMsg )
        {
            printWarning( $errorMsg );
            redirect( 'user/browse_equipments');
            return;
        }

        $dayPat = splitAtCommonDelimeters( $_POST['day_pattern'] );
        $dates = array();
        for ($i = 0; $i < intval($_POST['num_repeat']); $i++) 
        {
            foreach( $dayPat as $day )
                $dates[] = dbDate( "this $day", "+$i week");
        }

        $msg = '';
        foreach( $dates as $date )
        {
            $id = getUniqueID( 'equipment_bookings');
            $_POST['date'] = $date;
            $_POST['id'] = $id;
            $_POST['booked_by'] = whoAmI();
            $res = insertIntoTable( "equipment_bookings"
                    , 'id,equipment_id,date,start_time,end_time,booked_by,comment'
                    , $_POST 
                );
            if( $res )
                $msg .= "Successfully booked for $date with id $id. <br />";
        }

        flashMessage( $msg );
        redirect( 'user/browse_equipments');
    }

    public function cancel_equipment_booking( $id )
    {
        $res = updateTable( 'equipment_bookings', 'id', 'status'
            , array( 'id' => $id, 'status' => 'CACELLED' )
        );
        if($res)
            flashMessage( "Successfully cancelled booking.");

        redirect('user/browse_equipments');
    }

}

?>
