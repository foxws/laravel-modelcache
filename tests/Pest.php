<?php

beforeEach(function () {
    // Fake instances
    \Illuminate\Support\Facades\Bus::fake();
    \Illuminate\Support\Facades\Mail::fake();
    \Illuminate\Support\Facades\Notification::fake();
    \Illuminate\Support\Facades\Queue::fake();
    \Illuminate\Support\Facades\Storage::fake();
});
