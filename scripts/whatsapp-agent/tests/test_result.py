"""Tests for the Result dataclass used across whatsapp-agent services."""

import sys
import os

# Ensure the parent directory is importable so we can import services.result
sys.path.insert(0, os.path.join(os.path.dirname(__file__), ".."))

from services.result import Result


def test_ok_result():
    """Result.ok() returns a successful result."""
    r = Result.ok(data={"key": "value"})
    assert r.success is True
    assert r.error is None


def test_fail_result():
    """Result.fail() returns a failure result."""
    r = Result.fail("something went wrong")
    assert r.success is False
    assert r.error == "something went wrong"


def test_result_data():
    """Result.ok() carries data and Result.fail() leaves data as None."""
    r_ok = Result.ok(data=[1, 2, 3])
    assert r_ok.data == [1, 2, 3]

    r_fail = Result.fail("error")
    assert r_fail.data is None


def test_ok_without_data():
    """Result.ok() works with no data argument (defaults to None)."""
    r = Result.ok()
    assert r.success is True
    assert r.data is None
    assert r.error is None