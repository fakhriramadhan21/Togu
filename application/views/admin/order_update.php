<?php foreach ($cloths_edit as $row): ?>

		<div class="">
			<?php $attributes = array('class' => 'form-horizontal', 'id' => 'cloth_form','enctype' => 'multipart/form-data');
			echo form_open('admin/order_crud/modify/'.$row->invoice_no.'',$attributes); ?>
			
			<div class="form-group ">
				<label class="col-sm-3 control-label no-padding-right" for="cloth_id"> Invoice ID : </label>

				<div class="col-sm-9">
					<input type="text" id="cloth_id" class="form-control" required readonly value="<?php echo $row->invoice_no ;?>" />
				</div>
			</div>
			
			<div class="space-4"></div>
			
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="cloth_name"> Customer ID : </label>

				<div class="col-sm-9">
					<input type="text" name="cloth_name" class="form-control" value="<?php echo $row->customer_id ;?>" required  autofocus />
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="cloth_code"> Order Status : </label>

				<div class="col-sm-9">
					<!-- <input type="text" name="cloth_code" class="form-control" value="<?php echo $row->order_status ;?>" /> -->
					<select class="form-control" id="status" name="cloth_code">
						<option value="Paid" <?php if($row->order_status == "Paid"){echo'selected="selected"';} ;?> >Paid</option>
						<option value="Not Paid" <?php if($row->order_status == "Not Paid"){echo'selected="selected"';} ;?>>Not Paid</option>
					</select>
				</div>
			</div>


			<div class="clearfix form-actions">
				<div class="col-md-offset-3 col-md-9">
					<input class="btn btn-success" type="submit" value=" &nbsp;&nbsp; Update &nbsp;&nbsp;">
						
						&nbsp; &nbsp; &nbsp;														
						&nbsp; &nbsp; &nbsp;
					<input class="btn" type="reset" value="&nbsp;&nbsp; Reset &nbsp;&nbsp; ">
						
					
				</div>
			</div>
			
			</form>
		</div>
		
		<?php endforeach; ?>