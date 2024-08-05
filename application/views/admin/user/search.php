<div class="row">
	<ol class="breadcrumb">
		<li><a href="#"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
		<li class="active">Khách hàng</li>
	</ol>
</div><!--/.row-->
<h3><span id="message"></span></h3>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-info">
			<div class="panel-heading">
			<div class="col-md-4">Danh sách khách hàng</div>
			<div class="col-md-1"></div>
                <div class="col-md-6"  style="float:right;margin-top: 5px">
                    <form role="search" action='<?php echo admin_url('user/search'); ?>' method="post">
                        <div class="form-group">
                            <input name="search" type="text" class="form-control" placeholder="Nhập tên khách hàng">
                        </div>
                        <button class="btn text-right" style="position: absolute;right: 16px;top: 2px;float:right; padding: 4px 8px 4px 8px;" type="submit"><img src="<?php echo base_url(); ?>/upload/ic_search.png" /></button>
                    </form>
                </div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr class="info">										
								<th>ID</th>										
								<th>Họ tên</th>
								<th>Email</th>
								<th>Địa chỉ</th>										
								<th>Hành động</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							</tr>
							<?php foreach ($user as $value) { ?>
								<tr>
									<td><strong><?php echo $value->id; ?></strong></td>
									<td><strong ><?php echo $value->name; ?></strong></td>
									<td><strong ><?php echo $value->email; ?></strong></td>
									<td><strong ><?php echo $value->address; ?></strong></td>
									<td class="list_td aligncenter">
										<a href="<?php echo admin_url('user/order/'.$value->id); ?>" title="Danh sách đơn hàng"><span class="glyphicon glyphicon-list-alt"></span></a>&nbsp;&nbsp;&nbsp;
							            
							            <a href="<?php echo admin_url('user/del/'.$value->id); ?>" title="Xóa"> <span class="glyphicon glyphicon-remove" onclick=" return confirm('Bạn chắc chắn muốn xóa')"></span> </a>
							            
								    </td>    
				                </tr>
							<?php } ?>
			    		</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div><!--/.row-->
