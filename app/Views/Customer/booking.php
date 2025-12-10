<div class="booking-page-root">

    <!-- LEFT: BOOKING FORM -->
    <section class="booking-panel-left">
        <div class="booking-card-art">
            <div class="book-now-label"><img src="/assets/img/BookNow.svg" alt="Book Now"></div>

            <form id="bookForm" class="book-form" method="POST" action="/book">
                <input type="hidden" name="_csrf" value="<?= \App\Core\CSRF::getToken() ?>">

                <select name="service_id" id="serviceSelect" class="form-field">
                    <option value="">Service</option>

                    <?php
                    // Group services by category
                    $groups = [];
                    foreach ($services as $s) {
                        $cat = $s["category"] ?? "Other Services";
                        $groups[$cat][] = $s;
                    }
                    ?>

                    <?php foreach ($groups as $cat => $items): ?>
                    <option disabled style="font-weight:bold; color:#8A3DFF; background:#F4ECFF;">
                        — <?= htmlspecialchars($cat) ?> —
                    </option>

                    <?php foreach ($items as $it): ?>
                    <option value="<?= $it["service_id"] ?>" data-price="<?= htmlspecialchars($it["price"]) ?>">
                        <?= htmlspecialchars($it["service_name"]) ?> — ₱<?= number_format($it["price"]) ?>
                    </option>
                    <?php endforeach; ?>

                    <option disabled></option>
                    <?php endforeach; ?>
                </select>


                <div class="two-col">
                    <input name="date" id="apptDate" class="form-field" type="date" required>
                    <select name="time" id="apptTime" class="form-field">
                        <option value="">Appointment Time</option>
                    </select>
                </div>

                <textarea name="note" class="form-field textarea-field" placeholder="Note"></textarea>

                <button id="bookNowBtn" type="submit" class="book-btn">BOOK</button>
            </form>
        </div>
    </section>

    <!-- RIGHT: PRICE LIST -->
    <section class="price-list-right">
        <img class="pricelist-bg-left" src="/assets/img/Services_VerticalBackground.svg">

        <div class="price-card-shape">
            <div class="price-content">
                <?php
                $groups = [];
                foreach ($services as $s) {
                    $cat = $s["category"] ?? "Other Services";
                    $groups[$cat][] = $s;
                }
                ?>

                <div class="price-blocks">
                    <?php foreach ($groups as $cat => $items): ?>
                    <div class="price-category">
                        <h4 class="cat-title"><?= htmlspecialchars($cat) ?></h4>
                        <ul>
                            <?php foreach ($items as $it): ?>
                            <li class="price-row">
                                <span class="price-name"><?= htmlspecialchars($it["service_name"]) ?></span>
                                <span class="price-val">₱<?= number_format($it["price"]) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <img class="pricelist-bg-right" src="/assets/img/PriceList_VerticalBackground.svg">

    </section>

</div>

<script src="/assets/js/booking.js"></script>
<link rel="stylesheet" href="/assets/css/booking.css">