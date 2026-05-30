<?php

use App\Support\Base62;

test('encode and decode are inverses', function () {
    foreach ([1, 62, 1000, 999_999, 123_456_789] as $n) {
        expect(Base62::decode(Base62::encode($n)))->toBe($n);
    }
});

test('fromId pads to minimum length', function () {
    expect(strlen(Base62::fromId(1)))->toBeGreaterThanOrEqual(6);
});

test('fromId produces distinct slugs for sequential ids', function () {
    $a = Base62::fromId(1);
    $b = Base62::fromId(2);
    expect($a)->not->toBe($b);
});
