 <?php if (!empty($salesmans)) {?>
                            <?php $sl = 1 ?>
                            <?php foreach ($salesmans as $value) {?>
                            <tr>
                                <td><?php echo  $sl++ ?></td>
                                <td><?php echo  html_escape($value->salesman_name); ?></td>
                                <td><?php echo  html_escape($value->salesman_email); ?></td>
                                <td><?php echo  html_escape($value->salesman_mobile); ?></td>
                                <td><?php echo  html_escape((!empty($value->balance)?$value->balance:0)); ?></td>
                                <td>
                                  <a href="<?php echo base_url("salesman/salesman/form/$value->salesman_id") ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="pe-7s-note" aria-hidden="true"></i></a>  
                                 <a onclick="salesmandelete(<?php echo $value->salesman_id?>)" href="javascript:void(0)"  class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="pe-7s-trash" aria-hidden="true"></i></a>
                             </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>