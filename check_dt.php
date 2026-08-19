<?php
$content = file_get_contents('resources/views/admin/yudisium/index.blade.php');
$target = "<form action=\"{{ route('admin.yudisium.toggleActivePeriode', \$period->id) }}\" method=\"POST\">";
$replacement = "<form action=\"{{ route('admin.yudisium.toggleActivePeriode', \$period->id) }}\" method=\"POST\" class=\"d-inline\">";
$content = str_replace($target, $replacement, $content);
$target2 = "</form>
                                                </td>";
$replacement2 = "</form>
                                                    <form action=\"{{ route('admin.yudisium.periode.destroy', \$period->id) }}\" method=\"POST\" class=\"d-inline\" onsubmit=\"return confirm('Yakin ingin menghapus periode ini? Tindakan ini tidak dapat dibatalkan.');\">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type=\"submit\" class=\"btn btn-danger btn-sm shadow\" title=\"Hapus Periode\"><i class=\"fas fa-trash\"></i></button>
                                                    </form>
                                                </td>";
$content = str_replace($target2, $replacement2, $content);
file_put_contents('resources/views/admin/yudisium/index.blade.php', $content);
echo "View edited";
