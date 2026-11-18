#!/usr/bin/env bash
# Smoke test: opens the main pages of the running application and checks the HTTP status codes.
# Usage: bash .github/scripts/smokeTest.sh http://127.0.0.1:8000

set -euo pipefail

BASE_URL="${1:-http://127.0.0.1:8000}"
COOKIES="$(mktemp)"
FAILURES=0

expectStatus() {
    local expected="$1"
    local path="$2"
    shift 2
    local actual
    actual="$(curl -s -o /tmp/smoke-body.html -w '%{http_code}' -b "$COOKIES" -c "$COOKIES" "$@" "$BASE_URL$path")"

    if [[ "$actual" == "$expected" ]] && ! grep -qE "(Fatal error|Warning|Notice|Deprecated)(</b>)?:" /tmp/smoke-body.html; then
        echo "OK    $expected $path"
    else
        echo "FAIL  expected $expected, got $actual for $path"
        grep -E "(Fatal error|Warning|Notice|Deprecated)(</b>)?:|error-details" /tmp/smoke-body.html | head -5 || true
        FAILURES=$((FAILURES + 1))
    fi
}

echo "== Public pages =="
expectStatus 200 "/"
expectStatus 200 "/products"
expectStatus 200 "/products?category=creatina&search=crea&sort=price_asc"
expectStatus 200 "/products/creatina-monohidrat-300g"
expectStatus 200 "/about"
expectStatus 200 "/login"
expectStatus 200 "/register"
expectStatus 200 "/cart"
expectStatus 404 "/products/does-not-exist"
expectStatus 404 "/this-page-does-not-exist"

echo "== Guests are redirected from protected pages =="
expectStatus 302 "/orders"
expectStatus 302 "/admin"

echo "== Customer flow =="
expectStatus 302 "/register" -X POST \
    --data-urlencode "fullName=Test Client" \
    --data-urlencode "email=smoke-test@gymshop.test" \
    --data-urlencode "password=Smoke1234" \
    --data-urlencode "passwordConfirmation=Smoke1234"
expectStatus 302 "/cart/add" -X POST --data "productId=1&quantity=2"
expectStatus 200 "/cart"
expectStatus 200 "/checkout"
expectStatus 422 "/checkout" -X POST --data-urlencode "shippingAddress=short" --data-urlencode "phone=123"
expectStatus 302 "/checkout" -X POST \
    --data-urlencode "shippingAddress=Str. Testului nr. 1, București" \
    --data-urlencode "phone=0712345678"
expectStatus 200 "/orders"
expectStatus 403 "/admin"
expectStatus 403 "/orders/1"
expectStatus 302 "/logout" -X POST

echo "== Administrator flow =="
expectStatus 422 "/login" -X POST --data-urlencode "email=admin@gymshop.test" --data-urlencode "password=wrong-password"
expectStatus 302 "/login" -X POST --data-urlencode "email=admin@gymshop.test" --data-urlencode "password=Admin123!"
expectStatus 200 "/admin"
expectStatus 200 "/admin/products"
expectStatus 200 "/admin/products/create"
expectStatus 200 "/admin/products/1/edit"
expectStatus 302 "/admin/products" -X POST \
    --data-urlencode "name=Smoke Test Creatine" \
    --data-urlencode "categoryId=1" \
    --data-urlencode "brandId=1" \
    --data-urlencode "description=Product created automatically by the smoke test script." \
    --data-urlencode "price=49.90" \
    --data-urlencode "stock=5"
expectStatus 200 "/products/smoke-test-creatine"
expectStatus 200 "/admin/orders"
expectStatus 302 "/admin/orders/1/status" -X POST --data "status=shipped"
expectStatus 200 "/orders/1"
expectStatus 200 "/admin/logs"
expectStatus 200 "/admin/logs/download"

echo "== Activity log =="
for action in REGISTER CART_ADD ORDER_PLACED LOGIN LOGIN_FAILED ADMIN_PRODUCT_CREATE ADMIN_ORDER_STATUS; do
    if grep -q "| $action |" storage/logs/activity.log; then
        echo "OK    log contains $action"
    else
        echo "FAIL  log does not contain $action"
        FAILURES=$((FAILURES + 1))
    fi
done

rm -f "$COOKIES"

if [[ "$FAILURES" -gt 0 ]]; then
    echo "$FAILURES check(s) failed."
    exit 1
fi

echo "All smoke tests passed."
