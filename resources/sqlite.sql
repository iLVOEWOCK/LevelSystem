-- #!sqlite
-- # { levels
-- #  { initialize
CREATE TABLE IF NOT EXISTS levels (
    uuid VARCHAR(36) PRIMARY KEY,
    username VARCHAR(16),
    level INT DEFAULT 0
    );
-- #  }

-- #  { select
SELECT *
FROM levels;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :username string
-- #      :level int
INSERT OR REPLACE INTO levels(uuid, username, level)
VALUES (:uuid, :username, :level)
-- #  }

-- #  { update
-- #      :uuid string
-- #      :username string
-- #      :level int
UPDATE levels
SET username=:username,
    level=:level
WHERE uuid=:uuid;
-- #  }

-- #  { delete
-- #      :uuid string
DELETE FROM levels
WHERE uuid=:uuid;
-- #  }
-- # }