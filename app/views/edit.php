<?php
    $this->layout('templates/template', ['title' => 'Редактировать']);
?>
<main id="js-page-content" role="main" class="page-content mt-3">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-plus-circle'></i> Редактировать
        </h1>
    </div>
    <form action="/edit" method="post">
        <div class="row">
            <div class="col-xl-6">
                <div id="panel-1" class="panel">
                    <div class="panel-container">
                        <div class="panel-hdr">
                            <h2>Общая информация</h2>
                        </div>
                        <div class="panel-content">
                            <!-- username -->
                            <div class="form-group">
                                <label class="form-label" for="username">Имя</label>
                                <input name="username" type="text" id="simpleinput" class="form-control" value="<?=$user['username']?>">
                            </div>

                            <!-- title -->
                            <div class="form-group">
                                <label class="form-label" for="job_title">Место работы</label>
                                <input type="text" name="job_title" id="simpleinput" class="form-control" value="<?=$user['job_title']?>">
                            </div>

                            <!-- tel -->
                            <div class="form-group">
                                <label class="form-label" for="phone">Номер телефона</label>
                                <input type="text" name="phone" id="simpleinput" class="form-control" value="<?=$user['phone']?>">
                            </div>

                            <!-- address -->
                            <div class="form-group">
                                <label class="form-label" for="address">Адрес</label>
                                <input type="text" name="address" id="simpleinput" class="form-control" value="<?=$user['address']?>">
                            </div>
                            <div class="col-md-12 mt-3 d-flex flex-row-reverse">
                                <button class="btn btn-warning">Редактировать</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>