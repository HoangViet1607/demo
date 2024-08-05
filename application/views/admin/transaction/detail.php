<div class="row">
	<ol class="breadcrumb">
		<li><a href="#"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
		<li class="active">Chi tiết đơn đặt hàng</li>
	</ol>
</div><!--/.row-->

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-info">
			<div class="panel-body">
				<h3>Thông tin khách hàng</h3>
				<div class="table-responsive">
					<table class="table table-bordered">
					  <tbody>
					  	<tr>
					  		<td style="width: 100px">Họ và tên</td>
					  		<td><?php echo $transaction->user_name; ?></td>
					  	</tr>
					  	<tr>
					  		<td>Số điện thoại</td>
					  		<td><?php echo $transaction->user_phone; ?></td>
					  	</tr>
					  	<tr>
					  		<td>Địa chỉ</td>
					  		<td><?php echo $transaction->user_address; ?></td>
					  	</tr>
					  	<tr>
					  		<td>Tin nhắn</td>
					  		<td><?php echo $transaction->message; ?></td>
					  	</tr>
						  <tr>
					  		<td>Hình thức thanh toán</td>
					  		<td><?php echo $transaction->payment; ?></td>
					  	</tr>
					  	<tr>
					  		<td>Ngày đặt</td>
					  		<td><?php 
							$timestamp = strtotime($transaction->date);
							$new_format = date("H:i:s d/m/Y", $timestamp);
							echo $new_format; 
							?></td>
					  	</tr>
					  </tbody>
					</table>
				</div><br>
				<h3>Chi tiết đơn đặt hàng</h3>
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr class="info">										
								<th class="text-center">STT</th>
								<th>Tên sản phẩm</th>
								<th>Số lượng</th>
                                                                <th>Size</th>
								<th>Tổng Giá</th>
										
								
							</tr>
						</thead>
						<tbody>
							<?php 
							$stt = 0;
							foreach ($list_product as $value) { 
								$stt = $stt + 1 ;?>
								<tr>
									<td style="vertical-align: middle;text-align: center;"><strong><?php echo $stt ?></strong></td>
									<td><img src="<?php echo base_url(); ?>upload/product/<?php echo $value->image_link; ?>" alt="" style="width: 50px;float:left;margin-right: 10px;"><strong><?php echo $value->name; ?></strong>
									</td>
									<td style="vertical-align: middle"><strong ><?php echo $value->qty; ?></strong></td>
                                                                        <td style="vertical-align: middle"><strong ><?php echo $value->size_name; ?></strong></td>
                                                                        <td style="vertical-align: middle">
										<?php echo number_format($value->price); ?> VNĐ
									</td>
									
									   
				                </tr>
							<?php } ?>

			    		</tbody>

					</table>
					
					<?php  if  ($transaction->status  == 0) { ?>
    					<a href="<?php echo admin_url('transaction/accept1/'.$transaction->id); ?>" class="btn btn-success"> Xác nhận đơn hàng</a>
					<?php } elseif ($transaction->status == 1) { ?>
    					<a href="<?php echo admin_url('transaction/accept2/'.$transaction->id); ?>" class="btn btn-success"> Đang giao hàng</a>
					<?php } elseif ($transaction->status == 2) { ?>
    					<a href="<?php echo admin_url('transaction/accept3/'.$transaction->id); ?>" class="btn btn-success"> Giao hàng  thành  công</a> 
					<?php  }  ?> 
					

				</div>
			</div>
		</div>
	</div>
</div><!--/.row-->
